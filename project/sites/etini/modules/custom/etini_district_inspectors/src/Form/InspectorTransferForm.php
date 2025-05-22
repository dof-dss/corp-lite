<?php

namespace Drupal\etini_district_inspectors\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements admin form to allow transfer of schools to new inspector.
 */
class InspectorTransferForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inspector_transfer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $from_message = "Select the District Inspector that you would like to transfer schools <b>from</b>.";

    $to_message = "Select the District Inspector taht you would like to transfer schools <b>to</b>.";

    $form['site_url_list'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('District Inspector (old)'),
      '#target_type' => 'inspector',
      '#selection_handler' => 'views',
      '#selection_settings' => [
        'view' => [
          'view_name' => 'district_inspector_typeahead',
          'display_name' => 'inspector_reference',
          'arguments' => []
        ]
      ],
      '#description' => $this->t($from_message)
    ];

    $form['site_url_list_exclude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('District Inspector (new)'),
      '#description' => $this->t($to_message),
      '#default_value' => 'test2',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_keys = ['site_url_list', 'site_url_list_exclude'];

    foreach ($form_keys as $form_key) {
      if ($form_state->getValue($form_key)) {
        $replace_url_list = explode(PHP_EOL, $form_state->getValue($form_key));
        foreach ($replace_url_list as $replace_url) {
          // Make sure url is 'clean'.
          $replace_url = str_replace(["\n", "\t", "\r"], '', $replace_url);
          $pass = FALSE;
          if (preg_match('|^http:\/\/|', $replace_url) || preg_match('|^https:\/\/|', $replace_url)) {
            $pass = TRUE;
          }
          if (!$pass) {
            $form_state->setErrorByName($form_key, $this->t("URLs must start with 'http' or 'https'"));
          }
        }
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /*$this->config('unity_internal_link_checker.linksettings')
      ->set('site_url_list', $form_state->getValue('site_url_list'))
      ->set('site_url_list_exclude', $form_state->getValue('site_url_list_exclude'))
      ->save();*/
  }

}
