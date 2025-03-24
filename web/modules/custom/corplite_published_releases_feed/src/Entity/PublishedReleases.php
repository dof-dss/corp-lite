<?php

declare(strict_types=1);

namespace Drupal\corplite_published_releases_feed\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\corplite_published_releases_feed\PublishedReleasesInterface;

/**
 * Defines the published releases entity class.
 *
 * @ContentEntityType(
 *   id = "published_releases",
 *   label = @Translation("Published Releases"),
 *   label_collection = @Translation("Published Releasess"),
 *   label_singular = @Translation("published releases"),
 *   label_plural = @Translation("published releasess"),
 *   label_count = @PluralTranslation(
 *     singular = "@count published releasess",
 *     plural = "@count published releasess",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\corplite_published_releases_feed\PublishedReleasesListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\corplite_published_releases_feed\PublishedReleasesAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\corplite_published_releases_feed\Form\PublishedReleasesForm",
 *       "edit" = "Drupal\corplite_published_releases_feed\Form\PublishedReleasesForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "published_releases",
 *   admin_permission = "administer published_releases",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/published-releases",
 *     "add-form" = "/published-releases/add",
 *     "canonical" = "/published-releases/{published_releases}",
 *     "edit-form" = "/published-releases/{published_releases}/edit",
 *     "delete-form" = "/published-releases/{published_releases}/delete",
 *     "delete-multiple-form" = "/admin/content/published-releases/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.published_releases.settings",
 * )
 */
final class PublishedReleases extends ContentEntityBase implements PublishedReleasesInterface {

  use EntityChangedTrait;

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

    // This field holds the release date that appears in the feed.
    $fields['release_date'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Release date'))
      ->setDescription(t('Release date'))
      ->setRequired(TRUE);

    // This field is a Drupal internal field.
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the published releases was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // This field holds the updated date taht appears in the feed.
    $fields['updated'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Updated'))
      ->setDescription(t('Updated date'))
      ->setRequired(TRUE);

    // This field is a Drupal internal field.
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the published releases was last edited.'));

    return $fields;
  }

}
