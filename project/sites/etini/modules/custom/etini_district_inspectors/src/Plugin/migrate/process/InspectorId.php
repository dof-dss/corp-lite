<?php

namespace Drupal\etini_district_inspectors\Plugin\migrate\process;

use Drupal\etini_district_inspectors\Entity\Inspector;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides an 'InspectorId' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "inspector_id"
 * )
 */
class InspectorId extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Does this inspector already exist ?
    $inspector_id = NULL;
    $inspector_ids = \Drupal::entityQuery('inspector')
      ->condition('name', $value)
      ->accessCheck(false)
      ->execute();
    if (!empty($inspector_ids)) {
      // Inspector exists
      $inspector_id = reset($inspector_ids);
    } else {
      // Inspector does not exist, create it.
      $inspector = Inspector::create();
      $inspector->set('name', $value);
      $inspector->save();
      $inspector_id = $inspector->id();
    }
    return $inspector_id;
  }

}
