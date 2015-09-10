<?php

/**
 * @file
 * Contains \Drupal\visual_content_layout\Plugin\Swap\.
 */

namespace Drupal\visual_content_layout\Plugin\Swap;

use Drupal\visual_content_layout\SwapBase;

/**
 * Provides a 'Image' swap.
 *
 * @Swap(
 *   id = "img",
 *   name = @Translation("Image"),
 *   description = @Translation("Add an image."),
 *   tip = "[img url='url' WIDTH='width' HEIGHT='height' /] -> width and height optional. "
 * )
 */

class Image extends SwapBase {

  function processCallback($attrs, $text) {
    $attrs = $this->setAttrs(array(
      'url' => '',
      'width' => '',
      'height' => '',
    ),
      $attrs
    );

    return $this->theme($attrs,$text);
  }

  public function theme($attrs, $text) {
    if($attrs['width'] == '' || $attrs['height'] == '') {
      return '<img src="' . $attrs['url'] . '" />';
    }else{
      return '<img src="' . $attrs['url'] . '" height="' . $attrs['width'] . '" width="' . $attrs['height'] . '"/>';
    }
  }

}
