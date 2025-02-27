<?php

namespace Drupal\etini_district_inspectors\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the inspector entity class.
 *
 * @ContentEntityType(
 *   id = "inspector",
 *   label = @Translation("Inspector"),
 *   label_collection = @Translation("Inspectors"),
 *   label_singular = @Translation("inspector"),
 *   label_plural = @Translation("inspectors"),
 *   label_count = @PluralTranslation(
 *     singular = "@count inspectors",
 *     plural = "@count inspectors",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\etini_district_inspectors\InspectorListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\etini_district_inspectors\InspectorAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\etini_district_inspectors\Form\InspectorForm",
 *       "edit" = "Drupal\etini_district_inspectors\Form\InspectorForm",
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
 *   base_table = "inspector",
 *   revision_table = "inspector_revision",
 *   show_revision_ui = TRUE,
 *   admin_permission = "administer inspector",
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
 *     "collection" = "/admin/content/inspector",
 *     "add-form" = "/inspector/add",
 *     "canonical" = "/inspector/{inspector}",
 *     "edit-form" = "/inspector/{inspector}/edit",
 *     "delete-form" = "/inspector/{inspector}/delete",
 *     "delete-multiple-form" = "/admin/content/inspector/delete-multiple",
 *     "revision" = "/inspector/{inspector}/revision/{inspector_revision}/view",
 *     "revision-delete-form" = "/inspector/{inspector}/revision/{inspector_revision}/delete",
 *     "revision-revert-form" = "/inspector/{inspector}/revision/{inspector_revision}/revert",
 *     "version-history" = "/inspector/{inspector}/revisions",
 *   },
 *   field_ui_base_route = "entity.inspector.settings",
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

    return $fields;
  }

}
