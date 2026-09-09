<?php


namespace Ceneje\CSScripts;

use Ceneje\Helpers\Helper;
use Ceneje\Config\Config;

class TrustmarkScript
{

  public static function init()
  {
    add_action('wp_enqueue_scripts', function () {
      $scriptName = Config::$pluginSlug . 'Trustmark';
      wp_register_script($scriptName, Helper::asset('/js/frontend/csTrustmark.js'), null, 1.1, true);
      wp_enqueue_script($scriptName);
      $data = array(
        'shopId' => Config::$scripts['shopId']
      );
      wp_localize_script($scriptName, 'cenejeVars', $data);
    });
  }
}
