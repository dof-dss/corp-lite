<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\etini_district_inspectors\SchoolInterface;

/**
 * Defines the school entity class.
 *
 * @ContentEntityType(
 *   id = "school",
 *   label = @Translation("School"),
 *   label_collection = @Translation("Schools"),
 *   label_singular = @Translation("school"),
 *   label_plural = @Translation("schools"),
 *   label_count = @PluralTranslation(
 *     singular = "@count schools",
 *     plural = "@count schools",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\etini_district_inspectors\SchoolListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\etini_district_inspectors\SchoolAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\etini_district_inspectors\Form\SchoolForm",
 *       "edit" = "Drupal\etini_district_inspectors\Form\SchoolForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *       "revision-delete" = \Drupal\Core\Entity\Form\RevisionDeleteForm::class,
 *       "revision-revert" = \Drupal\Core\Entity\Form\RevisionRevertForm::class,
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *       "revision" = \Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider::class,
 *     },
 *   },
 *   base_table = "school",
 *   revision_table = "school_revision",
 *   show_revision_ui = TRUE,
 *   admin_permission = "administer school",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id"
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/school",
 *     "add-form" = "/school/add",
 *     "canonical" = "/school/{school}",
 *     "edit-form" = "/school/{school}/edit",
 *     "delete-form" = "/school/{school}/delete",
 *     "delete-multiple-form" = "/admin/content/school/delete-multiple",
 *     "revision" = "/school/{school}/revision/{school_revision}/view",
 *     "revision-delete-form" = "/school/{school}/revision/{school_revision}/delete",
 *     "revision-revert-form" = "/school/{school}/revision/{school_revision}/revert",
 *     "version-history" = "/school/{school}/revisions",
 *   },
 *   field_ui_base_route = "entity.school.settings",
 * )
 */
final class School extends RevisionableContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['Name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 128,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['de_reference'] = BaseFieldDefinition::create('string')
      ->setLabel(t('DE Reference'))
      ->setDescription(t('The DE Reference of the school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['phases'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Phases'))
      ->setDescription(t('The phases supported by this school'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['inspector_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Referenced Inspector'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'inspector')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ]);

    return $fields;
  }

}
