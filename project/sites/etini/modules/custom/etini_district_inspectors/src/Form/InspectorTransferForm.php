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
    $message = "All environment domains (Local, Edge, UAT, Main) are already being stripped of absolute links and changed to relative for this site.";
    $message .= "<br/>List any extra domain names here that must be stripped out of absolute links and changed to relative as they are saved.";
    $message .= "<br/>For example adding 'http://uregni.gov.uk' here will cause any links starting with that domain name to be saved as relative links instead.";
    $message .= "<br/>You may add as many domain names as you like, along with the appropriate 'http' or 'https' protocol.";

    $message_exclude = "If there are any specific URL's that are not to be made a relative URL then add the full URL in this field.";
    $message_exclude .= "<br/>For example adding https://employmenttribunalsni.co.uk/OITFET_IWS/Login.aspx will keep this link as it is.";
    $message_exclude .= "<br/>You may add as many domain names as you like, along with the appropriate 'http' or 'https' protocol.";

    $form['site_url_list'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site URLs'),
      '#description' => $this->t($message),
      '#default_value' => 'test1',
    ];

    $form['site_url_list_exclude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site URLs to exclude'),
      '#description' => $this->t($message_exclude),
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
