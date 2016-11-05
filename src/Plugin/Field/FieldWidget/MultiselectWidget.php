<?php

/**
 * @file
 * Contains \Drupal\multiselect\Plugin\Field\FieldWidget\MultiselectWidget.
 */

namespace Drupal\multiselect\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Select;
use Drupal\multiselect\Element\Multiselect;

/**
 * Plugin implementation of the 'multiselect' widget.
 *
 * @FieldWidget(
 *   id = "multiselect",
 *   label = @Translation("Multiselect"),
 *   field_types = {
 *     "list_string",
 *     "list_float",
 *     "list_integer",
 *     "entity_reference",
 *   },
 *   multiple_values = TRUE
 * )
 */
class MultiselectWidget extends OptionsWidgetBase {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    /** @var Multiselect $element */
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Prepare some properties for the child methods to build the actual form element.
    $this->required = $element['#required'];
    $this->multiple = $this->fieldDefinition->getFieldStorageDefinition()->isMultiple();
    $this->has_value = isset($items[0]->{$this->column});

    $element += array(
      '#type' => 'multiselect',
      '#name' => $items->getName(),
      '#size' => $this->getSetting('size'),
      '#options' => $this->getOptions($items->getEntity()),
      '#default_value' => $this->getSelectedOptions($items, $delta),
      // Do not display a 'multiple' select box if there is only one option.
      '#multiple' => $this->multiple && count($this->options) > 1,
    );

    // Run Select processSelect processing, sets up #multiple, name[], and more.
    Multiselect::processSelect($element, $form_state, $form);

    return $element;
  }

  /**
   * Form validation handler for widget elements.
   *
   * @param array $element
   *   The form element.
   * @param array $form_state
   *   The form state.
   */
  public static function validateElement(array $element, FormStateInterface $form_state) {
    // @todo fix multiselect validation.
    parent::validateElement($element, $form_state);
    // Massage submitted form values.
    // Drupal\Core\Field\WidgetBase::submit() expects values as
    // an array of values keyed by delta first, then by column, while our
    // widgets return the opposite.

    if (is_array($element ['#value'])) {
      $values = array_values($element ['#value']);
    }
    else {
      $values = array($element ['#value']);
    }

    // Filter out the 'none' option. Use a strict comparison, because
    // 0 == 'any string'.
    $index = array_search('_none', $values, TRUE);
    if ($index !== FALSE) {
      unset($values [$index]);
    }

    // Transpose selections from field => delta to delta => field.
    $items = array();
    foreach ($values as $value) {
      $items [] = array($element ['#key_column'] => $value);
    }
    $form_state->setValueForElement($element, $items);
  }

}
