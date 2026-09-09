=== Shopper's Mind ===
Contributors: cenejewebmaster, miloskostadinovski
Requires at least: 4.4.0
Tested up to: 5.7
Requires PHP: 8.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 
Woocommerce export for Shopper's Mind platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba), add CERTIFIED SHOP® Trustmark and much more.

== Description ==
Export your Woocommerce products through generated XML file to Shopper's Mind comparison shopping platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba).
You can customize your settings, select products to include/exclude from XML file, exclude “Out of Stock” products, configure additional products attributes (color, size, brand, etc),
define delivery costs and Free delivery threshold, delivery time, add CERTIFIED SHOP® Trustmark, Pop-up, Floater to your web shop and much more.

Copyright 2016 - 2021 Shopper's Mind

== Changelog ==

= 1.0.2 (fork) =
* Fixed broken CDATA tag in the pluginVersion XML node (was missing its opening `<![CDATA[`).
* Added a permission_callback to the REST export route to remove the WP 5.5+ _doing_it_wrong notice (feed remains public on purpose).
* Guarded several WooCommerce API calls (wc_get_product, get_term_by, wc_get_attribute) that could return false/null and crash the export with a fatal error.
* Raised the minimum PHP requirement to 8.1 and verified against PHPCompatibilityWP with zero findings.

= 1.0.2 =
* Tested plugin on the newest WP version (5.7)
 
= 1.0.1 =
* Added Wordpress version to XML feed.
* Added new CERTIFIED SHOP® Trustmark version.
 
= 1.0 =
* CERTIFIED SHOP® Trustmark, Pop-up, Floater
* Export of Woocommerce products through generated XML file to Shopper's Mind comparison shopping platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba).