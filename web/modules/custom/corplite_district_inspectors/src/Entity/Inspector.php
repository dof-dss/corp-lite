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
 * Defines the Inspector entity.
 *
 * @ingroup corplite_district_inspectors
 *
 * @ContentEntityType(
 *   id = "inspector",
 *   label = @Translation("District Inspector"),
 *   base_table = "inspector",
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
class Inspector extends RevisionableContentEntityBase implements ContentEntityInterface {

  /**
   * Specify fields.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('District Inspector Name'))
      ->setDescription(t('The name of the District Inspector'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 128,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    return $fields;
  }

}
