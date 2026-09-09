<?php

/**
 * Plugin Name:       Shopper's Mind (Fork by Goran Brbot)
 * Description:       Export your Woocommerce products (generate XML file) to Shopper's Mind comparison shopping platforms (ceneje.si, jeftinije.hr, idealno.rs, idealno.ba), add CERTIFIED SHOP® Trustmark and much more. Fork maintained at github.com/goranBrbot/wp-plugin-jeftinije, updated for PHP 8.1+/current WordPress.
 * Version:           1.0.3
 * Requires at least: 4.4.0
 * Requires PHP:      8.1
 * Author:            Shopper's Mind
 * Author URI:        https://smind.si
 * Update URI:        https://github.com/goranBrbot/wp-plugin-jeftinije
 * Licence:           GPLv2 or later
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016 - 2021 Shopper's Mind
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once  plugin_dir_path(__FILE__) . 'src/Helpers/Helper.php';
require_once  plugin_dir_path(__FILE__) . 'src/Config/Config.php';
require_once  plugin_dir_path(__FILE__) . 'src/Plugin.php';
require_once  plugin_dir_path(__FILE__) . 'src/Scripts/TrustmarkScript.php';
require_once  plugin_dir_path(__FILE__) . 'src/Scripts/PopupScript.php';
require_once  plugin_dir_path(__FILE__) . 'src/Scripts/FloaterScript.php';
require_once  plugin_dir_path(__FILE__) . 'src/Export/XMLEndpoint.php';
require_once  plugin_dir_path(__FILE__) . 'src/Export/WooCommerceExport.php';
require_once  plugin_dir_path(__FILE__) . 'src/Admin/AdminPluginForm.php';
require_once  plugin_dir_path(__FILE__) . 'src/Widgets/CsTrustmarkWidget.php';

new Ceneje\Plugin();