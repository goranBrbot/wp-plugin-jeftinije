<?php


namespace Ceneje;

use Ceneje\Config\Config;
use Ceneje\CSScripts\TrustmarkScript;
use Ceneje\CSScripts\FloaterScript;
use Ceneje\CSScripts\PopupScript;
use Ceneje\Export\XMLEndpoint;
use Ceneje\Helpers\Helper;

class Plugin
{

    function __construct()
    {
        $this->init();
    }

    private function init()
    {
        Config::init();

        // init CS frontend scripts 
        if (Config::$scripts['badgeEnabled']) {
            TrustmarkScript::init();
        }
        if (Config::$scripts['popupEnabled']) {
            PopupScript::init();
        }
        if (Config::$scripts['floaterEnabled']) {
            FloaterScript::init();
        }

        // create the XML Feed endpoint
        XMLEndpoint::init();

        // Register widgets
        add_action('widgets_init', function () {
            register_widget('Ceneje\Widgets\CsTrustmarkWidget');
        });

        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', function () {
            $scriptName = Config::$pluginSlug . 'csAdmin';
            wp_register_script($scriptName, Helper::asset('/js/admin/csAdmin.js'), array('jquery'), '1.0', true);
            wp_enqueue_script($scriptName);
            $data = array(
                'xmlFeedUrl' => Helper::getXmlFeedUrlPrefix()
            );
            wp_localize_script($scriptName, 'cenejeVars', $data);
        });
    }
}
