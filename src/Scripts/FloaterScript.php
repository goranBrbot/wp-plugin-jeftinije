<?php


namespace Ceneje\CSScripts;

use Ceneje\Helpers\Helper;
use Ceneje\Config\Config;

class FloaterScript
{
    public static function init()
    {
        add_action('wp_enqueue_scripts', function () {
            $scriptName = Config::$pluginSlug . 'Floater';
            wp_register_script($scriptName, Helper::asset('/js/frontend/csFloater.js'), null, 1.0, false);
            wp_enqueue_script($scriptName);
            $data = array(
                'shopId' => Config::$scripts['shopId']
            );
            wp_localize_script($scriptName, 'cenejeVars', $data);
        });
    }
}
