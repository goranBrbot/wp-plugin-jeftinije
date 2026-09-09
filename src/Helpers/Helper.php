<?php

namespace Ceneje\Helpers;

use Ceneje\Config\Config;

class Helper
{

    public static function asset($path)
    {
        return plugin_dir_url(__FILE__) . '../../resources' . $path;
    }

    public static function getXmlFeedUrlPrefix()
    {
        return get_site_url() . '/?rest_route=/' . Config::$export['endpointNamespace'] . '/';
    }

    public static function getWidgetSectionUrl()
    {
        return get_site_url() . '/wp-admin/widgets.php';
    }

}
