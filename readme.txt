=== Shopper's Mind (Fork by Goran Brbot) ===
Contributors: cenejewebmaster, miloskostadinovski, goranbrbot
Requires at least: 4.4.0
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Woocommerce export for Shopper's Mind platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba), add CERTIFIED SHOP® Trustmark and much more. This is an independent fork, updated for PHP 8.1+ and current WordPress — see https://github.com/goranBrbot/wp-plugin-jeftinije.

== Description ==
Export your Woocommerce products through generated XML file to Shopper's Mind comparison shopping platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba).
You can customize your settings, select products to include/exclude from XML file, exclude “Out of Stock” products, configure additional products attributes (color, size, brand, etc),
define delivery costs and Free delivery threshold, delivery time, add CERTIFIED SHOP® Trustmark, Pop-up, Floater to your web shop and much more.

This fork is maintained independently of the original wordpress.org listing by Goran Brbot (@goranBrbot) — https://github.com/goranBrbot/wp-plugin-jeftinije — to keep the plugin working on PHP 8.1+ and current WordPress releases.

Copyright 2016 - 2021 Shopper's Mind

== Changelog ==

= 1.0.4 =
* Fixed a fatal error on every wp-admin page if WooCommerce is deactivated while this plugin stays active: the settings-page bootstrap ran unconditionally on the global `admin_init` hook and called WooCommerce functions with no guard. Now the entire plugin bootstrap short-circuits (with an admin notice instead) when WooCommerce isn't active.
* Added a `Requires Plugins: woocommerce` header so WordPress blocks activation without WooCommerce present, and declared compatibility with WooCommerce High-Performance Order Storage (custom order tables) — the plugin never touches order data, but WooCommerce warns about undeclared plugins otherwise.
* Fixed a `wp_enqueue_script()` call using the wrong argument (passed a dependency array as the `$src` parameter), which silently corrupted the registered admin script and never actually declared the jQuery dependency it needs.
* Escaped the widget title output (`esc_html`) and an admin-settings help link (`esc_url`) that were missing output escaping.
* Changed the settings page's required capability from the role name `administrator` to the actual capability `manage_options`, per WordPress capability-check best practice.
* Fixed the XML feed URL setting to sanitize as a safe REST route segment instead of general-purpose text.
* Fixed three settings-sanitization callbacks that wiped a setting back to empty on invalid input instead of keeping the previous value.
* Renamed the widget class to match its filename (`CsTrustmarkWidget`) for consistency.

= 1.0.3 =
* Forked and adapted by Goran Brbot (@goranBrbot) for PHP 8.1+ / current WordPress compatibility: https://github.com/goranBrbot/wp-plugin-jeftinije
* Fixed broken CDATA tag in the pluginVersion XML node (was missing its opening `<![CDATA[`).
* Added a permission_callback to the REST export route to remove the WP 5.5+ _doing_it_wrong notice (feed remains public on purpose).
* Guarded several WooCommerce API calls (wc_get_product, get_term_by, wc_get_attribute) that could return false/null and crash the export with a fatal error.
* Fixed pluginVersion value reported in the XML feed (was hardcoded to 1.0.1, now matches the plugin version).
* Raised the minimum PHP requirement to 8.1 and verified against PHPCompatibilityWP (testVersion 8.1-) with zero findings.
* Renamed the plugin and added an Update URI header so WordPress stops matching this fork against the original wordpress.org listing (which was overriding this readme with the original's version/compatibility/changelog and would have offered an "update" that overwrites this fork with the unfixed original).

= 1.0.2 =
* Tested plugin on the newest WP version (5.7)
 
= 1.0.1 =
* Added Wordpress version to XML feed.
* Added new CERTIFIED SHOP® Trustmark version.
 
= 1.0 =
* CERTIFIED SHOP® Trustmark, Pop-up, Floater
* Export of Woocommerce products through generated XML file to Shopper's Mind comparison shopping platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba).