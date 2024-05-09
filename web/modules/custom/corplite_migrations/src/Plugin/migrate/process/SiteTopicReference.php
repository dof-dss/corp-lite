<?php

namespace Drupal\corplite_migrations\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides an 'SiteTopicReference' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "site_topic_reference"
 * )
 */
class SiteTopicReference extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $conn = Database::getConnection('default', 'default');
    // Loop through the topics in the field_site_topics field.
    foreach ($row->get('field_site_topics') as $topic) {
      \Drupal::logger('site_topic_reference')->notice("Row topic tid is " . $topic['tid']);
      $d7_tid = $topic['tid'];
      // Look up this D7 site topic tid to see which site topic nid it maps to on Drupal 10.
      $tid_query = $conn->query("select destid1 from migrate_map_upgrade_d7_taxonomy_term_site_topics where sourceid1 = $d7_tid");
      $new_tids = $tid_query->fetchAll();
      if (count($new_tids) > 0) {
        foreach ($new_tids as $new_tid) {
          \Drupal::logger('site_topic_reference')->notice("New nid is " . $new_tid->destid1);
        }
      }
    }
    \Drupal::logger('site_topic_reference')->notice("Row nid is " . $row->get('nid'));
    \Drupal::logger('site_topic_reference')->notice("************");
    return $value;
  }

}
