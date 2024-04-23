<?php

namespace Drupal\corplite_migrations\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Provides an 'TaxonomyRefFilter' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "taxonomy_ref_filter"
 * )
 */
class TaxonomyRefFilter extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $matches = [];
    // Look for all references to taxonomy terms in the text.
    if (preg_match_all('|\<a href=\"\/taxonomy\/term\/(\d*)"\>([^\<]*)\<\/a\>|', $value, $matches)) {
      if (count($matches) > 0) {
        object_log('matches', $matches);
        object_log('matches[0]', $matches[0]);
        object_log('matches[0][0]', $matches[0][0]);
        \Drupal::logger('topic_migration')->notice(t('Starting loop'));
        foreach ($matches[0] as $this_link) {
          object_log('this_link', $this_link);
          $msg = "Link is " . $this_link;
          \Drupal::logger('topic_migration')->notice(t($msg));
          //$value['value'] = str_replace($this->configuration['from_ref'], $this->configuration['to_ref'], $value['value']);
        }
      }
    }
    return $value;
  }

}
