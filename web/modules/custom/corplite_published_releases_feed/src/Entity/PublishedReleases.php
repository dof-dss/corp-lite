<?php

/**
 * @file
 * Contains PublishedReleases class.
 */

namespace Drupal\corplite_published_releases_feed\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the published releases entity.
 *
 * @ingroup published_releases
 *
 * @ContentEntityType(
 *   id = "published_releases",
 *   label = @Translation("Published Releases"),
 *   base_table = "published_releases",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   handlers = {
 *      "views_data" = "Drupal\views\EntityViewsData"
 *   }
 * )
 */
class PublishedReleases extends ContentEntityBase implements ContentEntityInterface {
  /**
   * Base field definitions.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the imported published release'))
      ->setReadOnly(TRUE)
      ->setSettings([
        'max_length' => 512,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The name of the imported published release'))
      ->setSettings([
        'max_length' => 512,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    $fields['summary'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Summary'))
      ->setDescription(t('Summary text for the imported published release'))
      ->setSettings([
        'max_length' => 1024,
        'text_processing' => 0,
      ])
      ->setRequired(FALSE);

    $fields['url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('URL'))
      ->setDescription(t('URL for the release document'))
      ->setSettings([
        'max_length' => 512,
        'text_processing' => 0,
      ])
      ->setRequired(FALSE);

    $fields['release_date'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Release date'))
      ->setDescription(t('Release date'))
      ->setRequired(TRUE);

    $fields['updated'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Updated'))
      ->setDescription(t('Updated date'))
      ->setRequired(TRUE);

    return $fields;
  }

}
