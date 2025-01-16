<?php

namespace Drupal\corplite_published_releases_feed\Plugin\facets\query_type;

use Drupal\facets\QueryType\QueryTypePluginBase;
use Drupal\facets\Result\Result;
use Drupal\search_api\Query\QueryInterface;

/**
 * Provides support for Availability facets within the Search API scope.
 *
 * @FacetsQueryType(
 *   id = "now_future_dates",
 *   label = @Translation("Availability | Now vs Future"),
 * )
 */
class NowFutureDateProcessorHandler extends QueryTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $query = $this->query;

    // Only alter the query when there's an actual query object to alter.
    if (!empty($query)) {
      $operator = $this->facet->getQueryOperator();
      $field_identifier = $this->facet->getFieldIdentifier();

      if ($query->getProcessingLevel() === QueryInterface::PROCESSING_FULL) {
        // Set the options for the actual query.
        $options = &$query->getOptions();
        $options['search_api_facets'][$field_identifier] = $this->getFacetOptions();
      }

      // Add the filter to the query if there are active values.
      $active_items = $this->facet->getActiveItems();

      if (count($active_items)) {
        // Add custom query condition
        $filter = $query->createConditionGroup($operator, ['facet:' . $field_identifier]);
        foreach ($active_items as $value) {
          $now = \Drupal::service('datetime.time')->getRequestTime();
          $comp_op = $value === 'now' ? '<=' : '>';
          $filter->addCondition($field_identifier, $now, $comp_op);
        }

        $query->addConditionGroup($filter);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $query_operator = $this->facet->getQueryOperator();
    $now = \Drupal::service('datetime.time')->getRequestTime();
    $new_facet_items = [
      'all' => 'All',
      'now' => 'Available Now',
      'future' => 'Available in the Future',
    ];

    if (!empty($this->results)) {
      $facet_results = [];
      $counts = [
        'now' => 0,
        'future' => 0,
        'all' => 0,
      ];

      // Loop through actual facet results and aggregate counts
      foreach ($this->results as $result) {
        if ($result['count'] || $query_operator == 'or') {
          $result_filter = trim($result['filter'], '"');
          if (!$result_filter) {
            continue;
          }

          $counts['all'] += $result['count'];

          if ($result_filter <= $now) {
            $counts['now'] += $result['count'];
          }
          else {
            $counts['future'] += $result['count'];
          }
        }
      }

      // Build new facet results
      foreach ($new_facet_items as $key => $label) {
        $count = $counts[$key];
        $result = new Result($this->facet, $key, $label, $count);
        $facet_results[] = $result;
      }

      $this->facet->setResults($facet_results);

      return $this->facet;
    }
  }

}
