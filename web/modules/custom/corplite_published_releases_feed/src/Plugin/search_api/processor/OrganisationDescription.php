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
 *   id = "org_description",
 *   label = @Translation("Organisation Description"),
 *   description = @Translation("Looks up the Organisation Description in the 'Organisations' vocabulary and stores it in the Solr index"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class OrganisationDescription extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL) {
    $properties = [];

    if ($datasource) {
      $definition = [
        'label' => $this->t('Organisation Description'),
        'description' => $this->t('The description for the organisation'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['organisation_description'] = new ProcessorProperty($definition);
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
      // Extract the organisation code that was sent in the feed.
      $org_code = $release->get('org')->getValue()[0]['value'];
      if (empty($org_code)) {
        return;
      }
      // Lookup the description for this code.
      $organisations_vocabulary = Vocabulary::load('organisations');
      $organisation_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'name' => $org_code,
        'vid' => $organisations_vocabulary->id()
      ]);
      if (empty($organisation_term)) {
        return;
      }
      $organisation_term = reset($organisation_term);
      $desc = strip_tags($organisation_term->get('description')->getValue()[0]['value']);
      if (!empty($desc)) {
        $fields = $this->getFieldsHelper()
          ->filterForPropertyPath($item->getFields(), $item->getDatasourceId(), 'organisation_description');
        foreach ($fields as $field) {
          $configuration = $field->getConfiguration();
          $field->addValue($desc);
        }
      }
    }
  }

}
