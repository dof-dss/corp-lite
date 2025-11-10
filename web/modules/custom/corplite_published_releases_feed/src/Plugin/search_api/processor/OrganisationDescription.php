<?php

namespace Drupal\corplite_published_releases_feed\Plugin\search_api\processor;

use Drupal\corplite_published_releases_feed\Entity\PublishedReleases;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Stores the organisation's description in the Solr index.
 *
 * @SearchApiProcessor(
 *   id = "org_description",
 *   label = @Translation("Organisation Description"),
 *   description = @Translation("Looks up the Organisation Description and stores in the Solr index"),
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
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
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
  public function addFieldValues(ItemInterface $item)
  {
    $datasourceId = $item->getDatasourceId();
    if ($datasourceId == 'entity:node') {
      // Retrieve the node in question.
      $release = PublishedReleases::Load($item->getOriginalObject()->getValue());
      // Extract the organisation code that was sent in the feed.
      $org_code = $release->get('org');
      // Lookup the description for this code.
      $organisation_text = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
        'name' => $org_code
      ]);
      $fields = $item->getFields(FALSE);
      $fields->addValue('Organisation desc');
    }
  }
}
