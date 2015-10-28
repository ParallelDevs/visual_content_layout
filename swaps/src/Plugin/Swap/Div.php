<?php

/**
 * @file
 * Contains \Drupal\visual_content_layout\Plugin\Swap\.
 */

namespace Drupal\swaps\Plugin\Swap;

use Drupal\swaps\SwapBase;

/**
 * Provides a 'Div' swap.
 *
 * @Swap(
 *   id = "swap_div",
 *   name = "Div",
 *   description = @Translation("Add div which you can add a bootstrap class."),
 *   attributes = "[Div Type | type | select | row : container]",
 *   container = true,
 *   tip = "[div class='row | container'] Content [/div]"
 * )
 */

class Div extends SwapBase {

  /**
   * Get all attributes of the swap and validate it.
   */
  public function processCallback($attrs, $text) {
    $attrs = $this->setAttrs(array(
      'extraclass' => '',
      'type' => 'container',
    ),
      $attrs
    );

    $attrs['type'] = $this->validateClass($attrs['type']);
    $attrs['style'] = $this->getStyle($attrs);

    return $this->theme($attrs, $text);
  }

  /**
   * Validate the class.
   */
  public function validateClass($class) {
    switch ($class) {
      case "row":
        return $class;

      case "container":
        return $class;

      default:
        return 'container';
    }
  }

  /**
   * Create the string of the swap.
   */
  public function theme($attrs, $text) {
    return '<div class="' . $attrs['type'] . ' '
    . $attrs['extraclass'] . '" ' . $attrs['style'] . '>' . $text . '</div>';
  }

}
