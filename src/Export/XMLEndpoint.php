<?php


namespace Ceneje\Export;

use Ceneje\Export\WooCommerceExport;
use Ceneje\Config\Config;

class XMLEndpoint
{

  public static function init()
  {
    add_action('rest_api_init', function () {
      register_rest_route(Config::$export['endpointNamespace'], Config::$export['endpointName'], array(
        'methods' => 'GET',
        'callback' => array(XMLEndpoint::class, 'handleEndpointRequest'),
        // feed is intentionally public so comparison-shopping platforms can fetch it unauthenticated
        'permission_callback' => '__return_true',
      ));
    });
  }

  public static function handleEndpointRequest()
  {
    WooCommerceExport::export();
    die;
  }
}
