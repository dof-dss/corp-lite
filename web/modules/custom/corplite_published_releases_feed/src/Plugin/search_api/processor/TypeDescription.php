<?php

namespace Drupal\corplite_published_releases_feed\Plugin\search_api\processor;

use Drupal\corplite_published_releases_feed\Entity\PublishedReleases;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Stores the organisation's description in the Solr index.
 *
 * @SearchApiProcessor(
 *   id = "type_description",
 *   label = @Translation("Type Description"),
 *   description = @Translation("Looks up the Type Description in the 'Document Types' vocabulary and stores it in the Solr index"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class TypeDescription extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL) {
    $properties = [];

    if ($datasource) {
      $definition = [
        'label' => $this->t('Type Description'),
        'description' => $this->t('The description for the document type'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['type_description'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $datasourceId = $item->getDatasourceId();
    if ($datasourceId == 'entity:published_releases') {
      // Retrieve the node in question.
      $obj = $item->getOriginalObject()->get('id')->getValue();
      if (is_array($obj) && isset($obj[0]) && isset($obj[0]['value'])) {
        $id = $obj[0]['value'];
      }
      else {
        return;
      }
      $release = PublishedReleases::Load($id);
      if (empty($release)) {
        return;
      }
      // Extract the document type code that was sent in the feed.
      $type_code = $release->get('type')->getValue()[0]['value'];
      if (empty($type_code)) {
        return;
      }
      // Lookup the description for this code.
      $document_type_vocabulary = Vocabulary::load('document_types');
      $type_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'name' => $type_code,
        'vid' => $document_type_vocabulary->id()
      ]);
      if (empty($type_term)) {
        return;
      }
      $type_term = reset($type_term);
      $desc = strip_tags($type_term->get('description')->getValue()[0]['value']);
      if (!empty($desc)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), $item->getDatasourceId(), 'type_description');
        foreach ($fields as $field) {
          $configuration = $field->getConfiguration();
          $field->addValue($desc);
        }
      }
    }
  }

}
