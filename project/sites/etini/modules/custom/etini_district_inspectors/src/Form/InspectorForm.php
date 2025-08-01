<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form controller for the inspector entity edit forms.
 */
final class InspectorForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->get('name')->getString()];
    $logger_args = [
      '%label' => $this->entity->get('name')->getString()
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New inspector %label has been created.', $message_args));
        $this->logger('etini_district_inspectors')->notice('New inspector %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The inspector %label has been updated.', $message_args));
        $this->logger('etini_district_inspectors')->notice('The inspector %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl(Url::fromUri('internal:/admin/content/inspector'));

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $name = $form_state->getValue('name')[0]['value'];
    if (!preg_match("/^[a-zA-Z' \-]+$/", $name)) {
      $form_state->setErrorByName(
        'name',
        $this->t('Inspector name must only contain letters, spaces, a hyphen or a single quote.'),
      );
    }
    parent::validateForm($form, $form_state);
  }

}
