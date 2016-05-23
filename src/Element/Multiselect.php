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

  /**
   * Helper function for get multiselect options.
   *
   * @param string $type
   *   The type of options.
   * @param $element
   *   An associative array containing the properties of the element.
   * @param $choices
   *   Mixed: Either an associative array of items to list as choices, or an
   *   object with an 'option' member that is an associative array. This
   *   parameter is only used internally and should not be passed.
   *
   * @return string
   *   An HTML string of options for the multiselect form element.
   */
  public static function getOptions($type, $element, $choices = NULL) {
    if (!isset($choices)) {
      if (empty($element['#options'])) {
        return '';
      }
      $choices = $element['#options'];
    }

    $options = array();

    // array_key_exists() accommodates the rare event where $element['#value'] is NULL.
    // isset() fails in this situation.
    $value_valid = isset($element['#value']) || array_key_exists('#value', $element);
    $value_is_array = $value_valid && is_array($element['#value']);
    // Check if the element is multiple select and no value has been selected.
    $empty_value = (empty($element['#value']) && !empty($element['#multiple']));
    foreach ($choices as $key => $choice) {
      if (is_array($choice)) {
        // @todo add support for optgroup.
        $options[] .= self::getOptions($type, $element, $choice);
      }
      elseif (is_object($choice) && isset($choice->option)) {
        $options[] .= self::getOptions($type, $element, $choice->option);
      }
      else {
        $key = (string) $key;
        $empty_choice = $empty_value && $key == '_none';
        switch ($type) {
          case 'available':
            if (!($value_valid && ((!$value_is_array && (string) $element['#value'] === $key || ($value_is_array && in_array($key, $element['#value']))) || $empty_choice))) {
              $options[] = array('type' => 'option', 'value' => $key, 'label' => $choice);
            }
            break;

          case 'selected':
            if ($value_valid && ((!$value_is_array && (string) $element['#value'] === $key || ($value_is_array && in_array($key, $element['#value']))) || $empty_choice)) {
              //$options .= '<option value="' . Html::escape($key) . '" selected="selected">' . Html::escape($choice) . '</option>';
              $options[] = array('type' => 'option', 'value' => $key, 'label' => $choice);
            }
            break;
        }
      }
    }
    return $options;
  }
}
