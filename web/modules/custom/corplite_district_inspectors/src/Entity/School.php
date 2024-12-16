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
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    $fields['de_reference'] = BaseFieldDefinition::create('string')
      ->setLabel(t('DE Reference'))
      ->setDescription(t('TThe DE Reference of the school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 12,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    return $fields;
  }
}
