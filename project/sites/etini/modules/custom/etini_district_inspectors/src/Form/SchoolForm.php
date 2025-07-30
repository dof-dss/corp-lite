<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\etini_district_inspectors\Entity\School;

/**
 * Form controller for the school entity edit forms.
 */
final class SchoolForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    // Load up the school.
    $entity = $this->buildEntity($form, $form_state);
    $school_id = $entity->id();

    // Retrieve the values set in the form.
    $inspector_id = $form_state->getValue('inspector_id');
    $name = $form_state->getValue('Name')[0]['value'];
    $de_reference = $form_state->getValue('de_reference')[0]['value'];
    $phases = $form_state->getValue('phases')[0]['value'];

    if (!empty($school_id)) {
      // Edit the school.
      $school = School::load($school_id);
      $result = NULL;
      if (!empty($school)) {
        $revision = clone $school;
        $revision->setNewRevision();
        $revision->set('inspector_id', $inspector_id);
        $revision->set('Name', $name);
        $revision->set('de_reference', $de_reference);
        $revision->set('phases', $phases);
        $revision->setRevisionCreationTime(\Drupal::time()->getCurrentTime());
        $revision->setRevisionUserId(\Drupal::currentuser()->id());
        $result = $revision->save();
      }
    } else {
      $school = School::create([
        'inspector_id' => $inspector_id,
        'Name' => $name,
        'de_reference' => $de_reference,
        'phases' => $phases
      ]);
      $school->save();
      $school_id = $school->id();
      $result = SAVED_NEW;
    }


    $message_args = ['%label' => $school->get('Name')->getString()];
    $logger_args = [
      '%label' => $school->get('Name')->getString(),
      '%inspector' => $inspector_id,
      '%schoolid' => $school_id
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New school %label has been created.', $message_args));
        $this->logger('etini_district_inspectors')->notice('New school %label has been created with id %schoolid.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The school id %label has been updated.', $message_args));
        $this->logger('etini_district_inspectors')->notice('The school %label has been updated, inspector id is %inspector.', $logger_args);
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
    $phases = ["Primary", "Nursery", "Post-primary", "EOTAS", "Prep", "Special"];
    if (!in_array($form_state->getValue('phases')[0]['value'], $phases)) {
      $form_state->setErrorByName(
        'phases',
        $this->t('Phase should be "Primary", "Nursery", "Post-primary", "EOTAS", "Prep" or "Special".'),
      );
    }
    $de_reference = $form_state->getValue('de_reference')[0]['value'];
    if ((strlen($de_reference) != 7) && ($de_reference != 'N/A')) {
      $form_state->setErrorByName(
        'de_reference',
        $this->t('DE Reference must be 7 digits or N/A.'),
      );
    }
    $name = $form_state->getValue('Name')[0]['value'];
    if (!preg_match("/^[a-zA-Z' ]+$/", $name)) {
      $form_state->setErrorByName(
        'Name',
        $this->t('School name must only contain letters, spaces or a single quote.'),
      );
    }
    parent::validateForm($form, $form_state);
  }

}
