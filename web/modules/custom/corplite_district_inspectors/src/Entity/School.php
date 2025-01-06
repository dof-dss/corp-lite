<?php

namespace Drupal\corplite_district_inspectors\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the school entity.
 *
 * @ingroup corplite_district_inspectors
 *
 * @ContentEntityType(
 *   id = "School",
 *   label = @Translation("School"),
 *   base_table = "school",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   handlers = {
 *      "views_data" = "Drupal\views\EntityViewsData"
 *   }
 * )
 */
class School extends RevisionableContentEntityBase implements ContentEntityInterface {
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['Name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 128,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    $fields['de_reference'] = BaseFieldDefinition::create('string')
      ->setLabel(t('DE Reference'))
      ->setDescription(t('The DE Reference of the school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setRequired(FALSE);

    $fields['c2k_email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('C2K Email'))
      ->setDescription(t('The C2K email for this inspector at this school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setRequired(FALSE);

    $fields['inspector_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Referenced Inspector'))
      ->setSetting('target_type', 'inspector');

    return $fields;
  }
}
