<?php

/**
 * @file
 * Contains \Drupal\multiselect\Element\Multiselect.
 */

namespace Drupal\multiselect\Element;
use Drupal\Core\Render\Element\Select;

/**
 * Provides a form element for displaying the label for a form element.
 *
 * Labels are generated automatically from element properties during processing
 * of most form elements.
 *
 * @FormElement("multiselect")
 */
class Multiselect extends Select {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#theme' => 'multiselect',
      '#input' => TRUE,
      '#multiple' => TRUE,
      '#default_value' => NULL,
      '#theme_wrappers' => array('form_element'),
      '#attached' => array(
        'library' => array('multiselect/drupal.multiselect'),
      ),
    );
  }

}
