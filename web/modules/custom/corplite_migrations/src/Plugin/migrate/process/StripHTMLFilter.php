<?php

namespace Drupal\corplite_migrations\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides a 'StripHTMLFilter' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "strip_html_filter"
 * )
 */
class StripHTMLFilter extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Reason for writing this filter was that a formatted text field in
    // Drupal 7 (the 'summary' field) was being converted to a plain
    // text field in Drupal 8.
    // Firstly, replace any html entities with their text
    // representation (including quotes).
    $value['value'] = html_entity_decode($value['value'], ENT_QUOTES);

    // Remove any HTML tags.
    $value['value'] = strip_tags($value['value']);

    return $value;
  }

}
