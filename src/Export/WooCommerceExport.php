<?php

namespace Ceneje\Export;

use Ceneje\Config\Config;
use Ceneje\Helpers\Helper;

class WooCommerceExport
{

  public static function export()
  {
    $config = array(
      'global-warranty' => '',            // e.g. '1 godina', if set it applies to all products
      'primary-cat' => '',                // e.g. Telefoni (optional)
      'attributes-to-skip' => array(
        Config::$export['genderAttribute'],
        Config::$export['colorAttribute'],
        Config::$export['sizeAttribute'],
        Config::$export['ageGroupAttribute'],
        Config::$export['brandAttribute'],
      )
    );

    $attrs = [];
    foreach ($config['attributes-to-skip'] as $id)
    {
      // 
      
      if ($id > 0)
      {
        $attribute = wc_get_attribute($id);
        if ($attribute)
        {
          array_push($attrs, 'attribute_' . $attribute->slug);
        }
      }
    }
    $config['attributes-to-skip-slugs'] = $attrs;

    // FETCH ALL PRODUCTS
    $args = array(
      'nopaging' => true,
      'status' => 'publish',
    );

    if (Config::$export['excludeOutOfStock'])
    {
      $args['stock_status'] = 'instock';
    }

    $products = wc_get_products($args);

    // SET XML CONTENT TXPE
    header("Content-type: text/xml");

    // START WITH OUTPUT
    echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
    echo "<CNJExport>\n";
    echo "<pluginVersion><![CDATA[Platform: Wordpress, Version: " . Config::$pluginVersion . "]]></pluginVersion>\n";
    foreach ($products as $product) :

      $id = $product->get_id();

      // CATEGORIES
      $categories = self::getProductCategories($id);
      $levelTwoCat = isset($categories) ? $categories : '';

      // DESCRIPTION
      // TAKE SHORT DESC IF LONG DESC IS EMPTY
      $desc = $product->get_description();
      if (empty($desc))
      {
        $desc = $product->get_short_description();
      }
      $desc =  wp_strip_all_tags(preg_replace("#\[[^\]]+\]#", '', $desc));

      // PRODUCT VARIATIONS
      if ($product->is_type( 'variable' ))
      {
          $availableVariationIds = $product->get_children();
          $groupId = !empty($product->get_sku()) ? $product->get_sku() : $id;
          foreach ($availableVariationIds as $variationId)
          {
            $variation = wc_get_product($variationId);
            if (!$variation)
              continue;
            if (Config::$export['excludeOutOfStock'] && !$variation->is_in_stock())
              continue;
            $productId = !empty($variation->get_sku()) ? $variation->get_sku() : $variation->get_id();
            
            // ATTRIBS, SKIP SOME
            $attribs = [];
            foreach ($variation->get_variation_attributes() as $attrName => $value)
            {
              if (in_array($attrName, $config['attributes-to-skip-slugs']))
              {
                continue;
              }

              $meta = get_post_meta($variation->get_id(), $attrName, true);
              $term = get_term_by('slug', $meta, str_replace('attribute_', '', $attrName));
              if (!$term)
              {
                continue;
              }

              $attribs[$attrName]['name'] = wc_attribute_label(str_replace('attribute_', '', $attrName));
              $attribs[$attrName]['values'][] = $term->name;
            }

            if ($productId == $groupId)
            {
              $productId = $variation->get_id();
            }

            self::echoProduct($variation, $productId, $desc, $levelTwoCat, $config, $groupId, $attribs );
          }

          // SKIP VARIABLE PRODUCT, WE ONLY ECHO VARIABLE PRODUCT VARIATIONS 
          continue;
      }

      // ATTRIBS, SKIP SOME
      $attribs = [];
      foreach ($product->get_attributes() as $attribute)
      {
        $attrName = $attribute->get_name();

        if (in_array($attribute->get_id(), $config['attributes-to-skip']))
        {
          continue;
        }

        $attribs[$attrName]['name'] = wc_attribute_label($attrName);
        if (!empty($attribute->get_terms()))
        {
          foreach ($attribute->get_terms() as $term)
          {
            $attribs[$attrName]['values'][] = $term->name;
          }
        } 
        else // custom attributes
        {
          foreach ($attribute->get_options() as $option)
          {
            $attribs[$attrName]['values'][] = $option;
          }
         
        }
      }

      $productId = !empty($product->get_sku()) ? $product->get_sku() : $id;
      self::echoProduct($product, $productId, $desc, $levelTwoCat, $config, null, $attribs);
    
    endforeach;

    echo "</CNJExport>";
  }

  private static function getProductCategories($id)
  {
    $productCategoryIds = array_unique(wc_get_product_cat_ids($id));
    if (empty($productCategoryIds))
    {
      return '';
    }
    $productCategoryId = $productCategoryIds[0];
    $categoriesIdHierarchy = array_merge(get_ancestors($productCategoryId, 'product_cat'), array($productCategoryId));
    $categoriesNameHierarchy = array();
    foreach ($categoriesIdHierarchy as $categoryId)
    {
      $term = get_term_by('id', $categoryId, 'product_cat');
      if ($term)
      {
        array_push($categoriesNameHierarchy, $term->name);
      }
    }
    $categories = implode(' > ', $categoriesNameHierarchy);
    return $categories;
  }

  private static function getAttributeValue($product, $attributeId)
  {
    $value = '';
    if ($attributeId > 0)
    {
      $attribute = wc_get_attribute($attributeId);
      if (!$attribute)
      {
        return $value;
      }
      $attributeName = $attribute->slug;
      $value = $product->get_attribute($attributeName);
      if (strpos($value, ',') !== false)
      {
        $value = '';
      }
    }
    return $value;
  }

  private static function generateAttributesInTitle($mainAttribs, $additionalAttribs)
  {
    $title = '';

    foreach($mainAttribs as $attribute)
    {
      if (!empty($attribute))
      {
        $title .= ' - ' . $attribute;
      }
    }

    foreach($additionalAttribs as $attribute)
    {
      if (!empty($attribute))
      {
        $title .= ' - ' . $attribute['values'][0];
      }
    }

    return $title;
  }

  private static function echoProduct($product, $productId, $desc, $levelTwoCat, $config, $groupId = null, $attribs = null )
  {

    // PREPARE OTHER IMAGES
    $productGallery = [];

    $attachmentIds = $product->get_gallery_image_ids();

    foreach ($attachmentIds as $attachmentId) 
    {
      $productGallery[] = wp_get_attachment_url($attachmentId);
    }

    // QUANTITY AND STOCK INFORMATION
    $qty = $product->get_stock_quantity();
    if ($product->managing_stock()) 
    {
      $stock = $qty > 0 ? 'in stock' : 'out of stock';
    } 
    elseif ($product->is_in_stock()) 
    {
      $stock = 'in stock';
    } 
    else 
    {
      $stock = 'out of stock';
    }

    // GENDER
    $genderAttributeId = Config::$export['genderAttribute'];
    $gender = self::getAttributeValue($product, $genderAttributeId);

    // COLOR
    $colorAttributeId = Config::$export['colorAttribute'];
    $color = self::getAttributeValue($product, $colorAttributeId);

    // SIZE
    $sizeAttributeId = Config::$export['sizeAttribute'];
    $size = self::getAttributeValue($product, $sizeAttributeId);

    // AGE GROUP
    $agegroupAttributeId = Config::$export['ageGroupAttribute'];
    $ageGroup = self::getAttributeValue($product, $agegroupAttributeId);

    // BRAND
    $brandAttributeId = Config::$export['brandAttribute'];
    $brand = self::getAttributeValue($product, $brandAttributeId);

    // DEFINE DELIVERY PRICE
    $delivery = Config::$export['deliveryCost'];
    $freeDeliveryAbove = Config::$export['freeDeliveryAbove'];

    //FREE DELIVERY IF AMOUNT LARGER THAN XX
    if ($freeDeliveryAbove !== '' && $product->get_price() >= floatval($freeDeliveryAbove))
    {
      $delivery = 0;
    }

    $attributesInTitle = self::generateAttributesInTitle([$gender, $color, $size, $ageGroup, $brand], $attribs);

    // ECHO ITEM
    // todo use some library to output xml
    echo "\t<Item>\n";
    echo "\t\t<ID><![CDATA[" . $productId . "]]></ID>\n";
    echo "\t\t<name><![CDATA[" . (str_replace('&#8211;', '–', $product->get_title())) . $attributesInTitle . "]]></name>\n";
    echo "\t\t<description><![CDATA[" . $desc . "]]></description>\n";
    echo "\t\t<link><![CDATA[" . $product->get_permalink() . "]]></link>\n";
    echo "\t\t<mainImage><![CDATA[" . wp_get_attachment_url($product->get_image_id(), 'full') . "]]></mainImage>\n";
    echo "\t\t<moreImages><![CDATA[" . implode(',', $productGallery) . "]]></moreImages>\n";
    echo "\t\t<price>" . number_format(floatval($product->get_price()), 2) . "</price>\n";
    if ($product->is_on_sale() && $product->get_regular_price() > 0) {
      echo "\t\t<regularPrice>" . number_format(floatval($product->get_regular_price()), 2) . "</regularPrice>\n";
    }
    echo "\t\t<curCode>" . get_woocommerce_currency() . "</curCode>\n";
    // echo "\t\t<stockText><![CDATA[" . $stockText . "]]></stockText>\n";
    echo "\t\t<stock>" . $stock . "</stock>\n";
    echo "\t\t<quantity>" . $qty . "</quantity>\n";
    echo "\t\t<fileUnder><![CDATA[" . ($config['primary-cat'] ? $config['primary-cat'] . " &gt; " : "") . $levelTwoCat . "]]></fileUnder>\n";
    echo "\t\t<brand><![CDATA[" . $brand . "]]></brand>\n";
    echo "\t\t<EAN></EAN>\n";
    echo "\t\t<productCode><![CDATA[" . $product->get_sku() . "]]></productCode>\n";
    // echo "\t\t<warranty><![CDATA[" . $config['global-warranty'] . "]]></warranty>\n";
    echo "\t\t<deliveryCost>" . ($delivery !== '' && $delivery >= 0 ? number_format($delivery, 2) : '') . "</deliveryCost>\n";
    echo "\t\t<deliveryTimeMin>" . (Config::$export['deliveryTimeMin'] > 0 ? Config::$export['deliveryTimeMin'] : '') . "</deliveryTimeMin>\n";
    echo "\t\t<deliveryTimeMax>" . (Config::$export['deliveryTimeMax'] > 0 ? Config::$export['deliveryTimeMax'] : '') . "</deliveryTimeMax>\n";

    if ($groupId != null)
    {
      echo "\t\t\t\t\t<groupId><![CDATA[" . $groupId . "]]></groupId>\n";
    }

    echo "\t\t<attributes>\n";

    echo "\t\t\t\t\t<gender><![CDATA[" . $gender . "]]></gender>\n";
    echo "\t\t\t\t\t<color><![CDATA[" . $color . "]]></color>\n";
    echo "\t\t\t\t\t<size><![CDATA[" . $size . "]]></size>\n";
    echo "\t\t\t\t\t<ageGroup><![CDATA[" . $ageGroup . "]]></ageGroup>\n";

    foreach ($attribs as $attrib) {
      echo "\t\t\t<attribute>\n";
      echo "\t\t\t\t<name><![CDATA[" . $attrib['name'] . "]]></name>\n";
      echo "\t\t\t\t<values>\n";

      foreach ($attrib['values'] as $val) {
        echo "\t\t\t\t\t<value><![CDATA[" . $val . "]]></value>\n";
      }

      echo "\t\t\t\t</values>\n";
      echo "\t\t\t</attribute>\n";
    }
    echo "\t\t</attributes>\n";

    echo "\t</Item>\n";
  }

}
