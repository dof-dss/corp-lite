<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\etini_district_inspectors\Entity\Inspector;

/**
 * Form controller for the inspector entity edit forms.
 */
final class InspectorForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    // Load up the inspector.
    $entity = $this->buildEntity($form, $form_state);
    $inspector_id = $entity->id();

    // Retrieve the values set in the form.
    $name = $form_state->getValue('name')[0]['value'];

    if (!empty($inspector_id)) {
      // Edit the inspector.
      $inspector = Inspector::load($inspector_id);
      $result = NULL;
      if (!empty($inspector)) {
        $revision = clone $inspector;
        $revision->setNewRevision();
        $revision->set('name', $name);
        $revision->setRevisionCreationTime(\Drupal::time()->getCurrentTime());
        $revision->setRevisionUserId(\Drupal::currentuser()->id());
        $result = $revision->save();
      }
    } else {
      $inspector = inspector::create([
        'name' => $name
      ]);
      $inspector->save();
      $inspector_id = $inspector->id();
      $result = SAVED_NEW;
    }


    $message_args = ['%label' => $inspector->get('name')->getString()];
    $logger_args = [
      '%label' => $inspector->get('name')->getString(),
      '%inspectorid' => $inspector_id
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New inspector %label has been created.', $message_args));
        $this->logger('etini_district_inspectors')->notice('New inspector %label has been created with id %inspectorid.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The inspector id %label has been updated.', $message_args));
        $this->logger('etini_district_inspectors')->notice('The inspector %label has been updated, inspector id is %inspector.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    //kint($form_state);
    $name = $form_state->getValue('name')[0]['value'];
    if (!preg_match("/^[a-zA-Z' ]+$/", $name)) {
      $form_state->setErrorByName(
        'name',
        $this->t('Inspector name must only contain letters, spaces or a single quote.'),
      );
    }
    parent::validateForm($form, $form_state);
  }

}
