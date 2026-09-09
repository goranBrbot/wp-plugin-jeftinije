<?php


namespace Ceneje\Widgets;

use Ceneje\Config\Config;

class CsTrustmarkWidget extends \WP_Widget
{
  function __construct()
  {
    parent::__construct(Config::$pluginSlug . 'Trustmark', 'CERTIFIED SHOP® Trustmark');
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget($args, $instance)
  {
    echo $args['before_widget'];
    if (!empty($instance['title'])) {
      echo $args['before_title'] . esc_html(apply_filters('widget_title', $instance['title'])) . $args['after_title'];
    }
    echo '<div class="smdWrapperTag"></div>';
    echo $args['after_widget'];
  }

}
