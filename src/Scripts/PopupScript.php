<?php


namespace Ceneje\CSScripts;

use Ceneje\Helpers\Helper;
use Ceneje\Config\Config;

class PopupScript
{

    public static function init()
    {
        add_action('wp_enqueue_scripts', function () {
            $scriptName = Config::$pluginSlug . 'Popup';
            if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
                wp_register_script($scriptName, Helper::asset('/js/frontend/csPopup.js'), null, 1.0, true);
                wp_enqueue_script($scriptName);
                $data = array(
                    'shopId' => Config::$scripts['shopId']
                );
                wp_localize_script($scriptName, 'cenejeVars', $data);
            }
        });
    }
}
