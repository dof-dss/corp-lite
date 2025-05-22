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

    $form['old_inspector_id'] = [
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

    $form['new_inspector_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('District Inspector (new)'),
      '#target_type' => 'inspector',
      '#selection_handler' => 'views',
      '#selection_settings' => [
        'view' => [
          'view_name' => 'district_inspector_typeahead',
          'display_name' => 'inspector_reference',
          'arguments' => []
        ]
      ],
      '#description' => $this->t($to_message)
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $from_id = $form_state->getValue('old_inspector_id');
    $to_id = $form_state->getValue('new_inspector_id');

    $message = "From is $from_id , to is $to_id";
    \Drupal::logger('etini_district_inspectors')->notice(t($message));

    /*$this->config('unity_internal_link_checker.linksettings')
      ->set('site_url_list', $form_state->getValue('site_url_list'))
      ->set('site_url_list_exclude', $form_state->getValue('site_url_list_exclude'))
      ->save();*/
  }

}
