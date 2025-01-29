<?php

namespace Drupal\corplite_published_releases_feed\Plugin\facets\processor;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\facets\FacetInterface;
use Drupal\facets\Plugin\facets\processor\UrlProcessorHandler;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Drupal\facets\Result\Result;
use Drupal\facets\Result\ResultInterface;

/**
 * Provides a processor that formats date facets.
 *
 * @FacetsProcessor(
 *   id = "example_processor",
 *   label = @Translation("Date grouping"),
 *   description = @Translation("Date grouping - group future and past dates"),
 * )
 */
class ExampleProcessor extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   *
   * String corresponds to a key on the $query_types array as defined
   * within hook_facets_search_api_query_type_mapping_alter().
   *
   * @see hook_facets_search_api_query_type_mapping_alter()
   */
  public function getQueryType() {
    return 'example';
  }

}
