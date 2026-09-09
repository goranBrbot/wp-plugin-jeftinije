<?php

use Ceneje\Config\Config;
use Ceneje\Helpers\Helper;

add_action('admin_menu', 'ceneje_plugin_menu');
function ceneje_plugin_menu()
{
  add_menu_page(
    "Shopper's mind",                  // The title to be displayed in the browser window for this page.
    "Shopper's mind",                  // The text to be displayed for this menu item
    'manage_options',                  // Which type of users can see this menu item
    'ceneje_export_plugin_options',    // The unique ID - that is, the slug - for this menu item
    'ceneje_export_plugin_display'     // The name of the function to call when rendering the page for this menu
  );

}

function ceneje_export_plugin_display()
{
?>
  <?php
    $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'xml_options';
  ?>

  <h2 class="nav-tab-wrapper">
    <a href="?page=ceneje_export_plugin_options&tab=xml_options" class="nav-tab <?php echo $active_tab == 'xml_options' ? 'nav-tab-active' : ''; ?>">XML Feed</a>
    <a href="?page=ceneje_export_plugin_options&tab=cs_options" class="nav-tab <?php echo $active_tab == 'cs_options' ? 'nav-tab-active' : ''; ?>">CERTIFIED SHOP®</a>
  </h2>

  <form method="POST" action="options.php">
    <?php 
      settings_errors();
      echo '<table class="form-table">'; // Must to add this wrapper to keep it like WordPress default UI
      if( $active_tab == 'xml_options' )
      {
        settings_fields('ceneje_export_plugin_options');  //pass slug name of page, also referred
        do_settings_fields('ceneje_export_plugin_options', 'ceneje_plugin_export_setting_section');   //pass slug name of page and section name
      } 
      else if( $active_tab == 'cs_options' )
      {
        settings_fields('ceneje_scripts_plugin_options');  //pass slug name of page, also referred
        do_settings_fields('ceneje_export_plugin_options', 'ceneje_plugin_scripts_setting_section');   //pass slug name of page and section name
      }
      echo '</table>';

      submit_button();
    ?>
  </form>
<?php
}

add_action('admin_init', 'ceneje_settings_api_init');
function ceneje_settings_api_init()
{

  $currenySymbol = get_woocommerce_currency_symbol();

  // Add the section to plugins settings so we can add our
  // fields to it
  add_settings_section(
    'ceneje_plugin_scripts_setting_section',
    'CERTIFIED SHOP® Scripts Settings',
    'ceneje_plugin_setting_scripts_section_callback_function',
    'ceneje_export_plugin_options'
  );

  // Add the field with the names and function to use for our new
  // settings, put it in our new section
  add_settings_field(
    'ceneje_plugin_shop_id_setting_name',
    'Your CERTIFIED SHOP® ID',
    'ceneje_plugin_shop_id_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_scripts_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_badge_enabled_setting_name',
    'Show Trustmark',
    'ceneje_plugin_badge_enabled_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_scripts_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_popup_enabled_setting_name',
    'Show popup',
    'ceneje_plugin_popup_enabled_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_scripts_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_floater_enabled_setting_name',
    'Show floating bar',
    'ceneje_plugin_floater_enabled_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_scripts_setting_section'
  );

  // Register our setting so that $_POST handling is done for us and
  // our callback function just has to echo the <input>
  register_setting('ceneje_scripts_plugin_options', 'ceneje_shop_id', 'ceneje_sanitize_string_input');
  register_setting('ceneje_scripts_plugin_options', 'ceneje_badge_enabled', 'ceneje_sanitize_bool_input');
  register_setting('ceneje_scripts_plugin_options', 'ceneje_popup_enabled', 'ceneje_sanitize_bool_input');
  register_setting('ceneje_scripts_plugin_options', 'ceneje_floater_enabled', 'ceneje_sanitize_bool_input');


  // Add the section to plugins settings so we can add our
  // fields to it
  add_settings_section(
    'ceneje_plugin_export_setting_section',
    'Shopper\'s Mind Export Settings',
    'ceneje_plugin_setting_export_section_callback_function',
    'ceneje_export_plugin_options'
  );

  // Add the field with the names and function to use for our new
  // settings, put it in our new section
  add_settings_field(
    'ceneje_plugin_xml_url_setting_name',
    'XML Feed URL',
    'ceneje_plugin_xml_url_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_exclude_out_of_stock_setting_name',
    "Exclude \"out of stock\" products",
    'ceneje_plugin_exclude_out_of_stock_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_gender_attribute_setting_name',
    'Gender attribute',
    'ceneje_plugin_gender_attribute_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_color_attribute_setting_name',
    'Color attribute',
    'ceneje_plugin_color_attribute_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_size_attribute_setting_name',
    'Size attribute',
    'ceneje_plugin_size_attribute_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_agegroup_attribute_setting_name',
    'Age group attribute',
    'ceneje_plugin_agegroup_attribute_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );
  
  add_settings_field(
    'ceneje_plugin_brand_attribute_setting_name',
    'Brand attribute',
    'ceneje_plugin_brand_attribute_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_delivery_cost_setting_name',
    "Delivery cost ($currenySymbol)",
    'ceneje_plugin_delivery_cost_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_free_delivery_above_setting_name',
    "Free delivery above ($currenySymbol)",
    'ceneje_plugin_free_delivery_above_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_delivery_time_min_above_setting_name',
    'Delivery time min (days)',
    'ceneje_plugin_delivery_time_min_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  add_settings_field(
    'ceneje_plugin_delivery_time_max_above_setting_name',
    'Delivery time max (days)',
    'ceneje_plugin_delivery_time_max_setting_callback_function',
    'ceneje_export_plugin_options',
    'ceneje_plugin_export_setting_section'
  );

  register_setting('ceneje_export_plugin_options', 'ceneje_xml_url', 'ceneje_sanitize_route_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_exclude_out_of_stock', 'ceneje_sanitize_bool_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_gender_attribute', 'ceneje_sanitize_wc_attribute_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_color_attribute', 'ceneje_sanitize_string_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_size_attribute', 'ceneje_sanitize_string_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_agegroup_attribute', 'ceneje_sanitize_string_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_brand_attribute', 'ceneje_sanitize_string_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_delivery_cost', 'ceneje_sanitize_number_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_free_delivery_above', 'ceneje_sanitize_number_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_delivery_time_min', 'ceneje_sanitize_number_input');
  register_setting('ceneje_export_plugin_options', 'ceneje_delivery_time_max', 'ceneje_sanitize_number_input');
}


// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function
// will be run at the start of our section
//
function ceneje_plugin_setting_scripts_section_callback_function()
{
  echo '<p>Configure your CERTIFIED SHOP® script</p>';
}

function ceneje_plugin_setting_export_section_callback_function()
{
  echo '<p>Configure your Shopper\'s Mind export settings</p>';
}

// ------------------------------------------------------------------
// Callback functions for scripts section settings
// ------------------------------------------------------------------
//
function ceneje_plugin_shop_id_setting_callback_function()
{
  $value = esc_attr(get_option('ceneje_shop_id'));
  echo "<input name=\"ceneje_shop_id\" id=\"ceneje_shop_id\" type=\"text\" class=\"code\" value=\"{$value}\"" . ' />';
  echo " e.g. Slo_123";
}

function ceneje_plugin_badge_enabled_setting_callback_function()
{
  $link = esc_url(Helper::getWidgetSectionUrl());
  $link = "<a href='$link'>\"Widgets\" section</a>";
  echo '<input name="ceneje_badge_enabled" id="ceneje_badge_enabled" type="checkbox" value="1" class="code" ' . checked(1, esc_attr(get_option('ceneje_badge_enabled')), false) . ' />';
  echo " Define where to put your Trustmark on your website in $link.";
}

function ceneje_plugin_popup_enabled_setting_callback_function()
{
  echo '<input name="ceneje_popup_enabled" id="ceneje_popup_enabled" type="checkbox" value="1" class="code" ' . checked(1, esc_attr(get_option('ceneje_popup_enabled')), false) . ' />';
}

function ceneje_plugin_floater_enabled_setting_callback_function()
{
  echo '<input name="ceneje_floater_enabled" id="ceneje_floater_enabled" type="checkbox" value="1" class="code" ' . checked(1, esc_attr(get_option('ceneje_floater_enabled')), false) . ' />';
}

// ------------------------------------------------------------------
// Callback functions for export section settings
// ------------------------------------------------------------------
//
function ceneje_plugin_xml_url_setting_callback_function()
{
  $value = esc_attr(Config::$export['endpointName']);
  echo esc_url(Helper::getXmlFeedUrlPrefix());
  echo "<input name=\"ceneje_xml_url\" id=\"ceneje_xml_url\" type=\"text\" class=\"code\" value=\"{$value}\"" . ' />';
  echo " <a href='' id='shoppersMindXmlUrl' style='text-decoration: underline'>Copy URL</a>";
}

function ceneje_plugin_exclude_out_of_stock_setting_callback_function()
{
  echo '<input name="ceneje_exclude_out_of_stock" id="ceneje_exclude_out_of_stock" type="checkbox" value="1" class="code" ' . checked(1, esc_attr(get_option('ceneje_exclude_out_of_stock', true)), false) . ' />';
}

function ceneje_plugin_gender_attribute_setting_callback_function() {
  $attributes = wc_get_attribute_taxonomies();
  echo "<select name=\"ceneje_gender_attribute\">";
  echo "<option value=\"-1\">" . __("&mdash; Select &mdash;") . "</option>";
  foreach ($attributes as $attribute) {
    $selected = selected(get_option('ceneje_gender_attribute'), $attribute->attribute_id);
    echo "<option value=\"{$attribute->attribute_id}\" {$selected}>" . $attribute->attribute_name . "</option>";
  }
  echo "</select>";
}

function ceneje_plugin_color_attribute_setting_callback_function() {
  $attributes = wc_get_attribute_taxonomies();
  echo "<select name=\"ceneje_color_attribute\">";
  echo "<option value=\"-1\">" . __("&mdash; Select &mdash;") . "</option>";
  foreach ($attributes as $attribute) {
    $selected = selected(get_option('ceneje_color_attribute'), $attribute->attribute_id);
    echo "<option value=\"{$attribute->attribute_id}\" {$selected}>" . $attribute->attribute_name . "</option>";
  }
  echo "</select>";
}

function ceneje_plugin_size_attribute_setting_callback_function() {
  $attributes = wc_get_attribute_taxonomies();
  echo "<select name=\"ceneje_size_attribute\">";
  echo "<option value=\"-1\">" . __("&mdash; Select &mdash;") . "</option>";
  foreach ($attributes as $attribute) {
    $selected = selected(get_option('ceneje_size_attribute'), $attribute->attribute_id);
    echo "<option value=\"{$attribute->attribute_id}\" {$selected}>" . $attribute->attribute_name . "</option>";
  }
  echo "</select>";
}

function ceneje_plugin_agegroup_attribute_setting_callback_function() {
  $attributes = wc_get_attribute_taxonomies();
  echo "<select name=\"ceneje_agegroup_attribute\">";
  echo "<option value=\"-1\">" . __("&mdash; Select &mdash;") . "</option>";
  foreach ($attributes as $attribute) {
    $selected = selected(get_option('ceneje_agegroup_attribute'), $attribute->attribute_id);
    echo "<option value=\"{$attribute->attribute_id}\" {$selected}>" . $attribute->attribute_name . "</option>";
  }
  echo "</select>";
}

function ceneje_plugin_brand_attribute_setting_callback_function()
{
  $attributes = wc_get_attribute_taxonomies();
  echo "<select name=\"ceneje_brand_attribute\">";
  echo "<option value=\"-1\">" . __("&mdash; Select &mdash;") . "</option>";
  foreach ($attributes as $attribute) {
    $selected = selected(get_option('ceneje_brand_attribute'), $attribute->attribute_id);
    echo "<option value=\"{$attribute->attribute_id}\" {$selected}>" . $attribute->attribute_name . "</option>";
  }
  echo "</select>";
}

function ceneje_plugin_delivery_cost_setting_callback_function()
{
  $value = esc_attr(get_option('ceneje_delivery_cost'));
  echo "<input name=\"ceneje_delivery_cost\" id=\"ceneje_delivery_cost\" type=\"text\" class=\"code\" value=\"{$value}\"" . ' />';
  echo ' e.g. 5';
}

function ceneje_plugin_free_delivery_above_setting_callback_function()
{
  $value = esc_attr(get_option('ceneje_free_delivery_above'));
  echo "<input name=\"ceneje_free_delivery_above\" id=\"ceneje_free_delivery_above\" type=\"text\" class=\"code\" value=\"{$value}\"" . ' />';
  echo ' e.g. 50';
}

function ceneje_plugin_delivery_time_min_setting_callback_function()
{
  $value = esc_attr(get_option('ceneje_delivery_time_min'));
  echo "<input name=\"ceneje_delivery_time_min\" id=\"ceneje_delivery_time_min\" type=\"text\" class=\"code\" value=\"{$value}\"" . ' />';
  echo ' e.g. 5';
}

function ceneje_plugin_delivery_time_max_setting_callback_function()
{
  $value = esc_attr(get_option('ceneje_delivery_time_max'));
  echo "<input name=\"ceneje_delivery_time_max\" id=\"ceneje_delivery_time_max\" type=\"text\" class=\"code\" value=\"{$value}\"" . ' />';
  echo ' e.g. 20';
}

// ------------------------------------------------------------------
// Callback functions for validating and sanitizing input functions
// ------------------------------------------------------------------
// 
function ceneje_sanitize_string_input($input)
{
  return sanitize_text_field(stripslashes($input));
}

// Used for the REST route path segment (register_rest_route()), so it's restricted to
// characters that are safe there instead of general-purpose text sanitization.
function ceneje_sanitize_route_input($input)
{
  return preg_replace('#[^a-zA-Z0-9/_-]#', '', sanitize_text_field(stripslashes($input)));
}

// On invalid input, fall back to the previously stored value instead of wiping the setting
// (register_setting() hooks this to the sanitize_option_{$option} filter, so current_filter()
// reliably gives us the option name to look up).
function ceneje_previous_option_value()
{
  return get_option(str_replace('sanitize_option_', '', current_filter()));
}

function ceneje_sanitize_number_input($input)
{
  if (!empty($input) && (!intval($input) || $input < 1))
  {
    add_settings_error(
      'ceneje_scripts_plugin_options',
      esc_attr( 'ceneje_settings_validation_error' ),
      'Only positive numbers allowed',
      'error'
    );
    return ceneje_previous_option_value();
  }

  return sanitize_text_field(stripslashes($input));
}

function ceneje_sanitize_bool_input($input)
{
  if (!empty($input) && $input != 1)
  {
    add_settings_error(
      'ceneje_scripts_plugin_options',
      esc_attr( 'ceneje_settings_validation_error' ),
      'Only boolean values allowed',
      'error'
    );
    return ceneje_previous_option_value();
  }

  return sanitize_text_field(stripslashes($input));
}

function ceneje_sanitize_wc_attribute_input($input)
{
  $attributes = wc_get_attribute_taxonomies();
  $attributes = array_map(function($attribute) {
    return $attribute->attribute_id;
  }, $attributes);

  if (!in_array($input, $attributes) && $input != -1)
  {
    add_settings_error(
      'ceneje_scripts_plugin_options',
      esc_attr( 'ceneje_settings_validation_error' ),
      'Only existing attribute values allowed',
      'error'
    );
    return ceneje_previous_option_value();
  }

  return sanitize_text_field(stripslashes($input));
}
