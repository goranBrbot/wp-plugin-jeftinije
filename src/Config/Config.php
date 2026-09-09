<?php


namespace Ceneje\Config;


class Config
{

    public static $pluginSlug = 'shoppersMind';
    public static $pluginVersion = '1.0.4';
    public static $scripts = array();
    public static $export = array();

    public static function init()
    {
        self::$scripts['shopId'] = get_option('ceneje_shop_id', '');
        self::$scripts['badgeEnabled'] = boolval(get_option('ceneje_badge_enabled', false));
        self::$scripts['popupEnabled'] = boolval(get_option('ceneje_popup_enabled', false));
        self::$scripts['popupPage'] = intval(get_option('ceneje_popup_page', 0));
        self::$scripts['floaterEnabled'] = boolval(get_option('ceneje_floater_enabled', false));

        self::$export['endpointNamespace'] = 'shoppersmind';
        self::$export['endpointName'] = get_option('ceneje_xml_url', 'v1/exportProducts');
        self::$export['excludeOutOfStock'] = boolval(get_option('ceneje_exclude_out_of_stock', true));
        self::$export['genderAttribute'] = intval(get_option('ceneje_gender_attribute', -1));
        self::$export['colorAttribute'] = intval(get_option('ceneje_color_attribute', -1));
        self::$export['sizeAttribute'] = intval(get_option('ceneje_size_attribute', -1));
        self::$export['ageGroupAttribute'] = intval(get_option('ceneje_agegroup_attribute', -1));
        self::$export['brandAttribute'] = intval(get_option('ceneje_brand_attribute', -1));
        self::$export['deliveryCost'] = get_option('ceneje_delivery_cost', '');
        self::$export['freeDeliveryAbove'] = get_option('ceneje_free_delivery_above', '');
        self::$export['deliveryTimeMin'] = intval(get_option('ceneje_delivery_time_min', -1));
        self::$export['deliveryTimeMax'] = intval(get_option('ceneje_delivery_time_max', -1));
    }
}
