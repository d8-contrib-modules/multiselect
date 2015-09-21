<?php

/**
 * @file
 * Contains \Drupal\multiselect\Form\SettingsForm.
 */

namespace Drupal\multiselect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form builder for the admin display defaults page.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multiselect_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('multiselect.settings');

    $form['basic'] = array();
    $form['basic']['multiselect_widths'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Width of Select Boxes (in pixels)'),
      '#default_value' => $config->get('multiselect.widths'),
      '#size' => 3,
      '#field_suffix' => 'px',
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('multiselect.settings')
      ->set('multiselect.widths', $form_state['values']['multiselect_widths'])
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['multiselect.settings'];
  }

}
