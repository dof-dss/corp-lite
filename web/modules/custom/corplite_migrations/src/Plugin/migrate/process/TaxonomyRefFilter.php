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
    if (preg_match_all('|\<a href=\"\/taxonomy\/term\/(\d*)"\>[^\<]*\<\/a\>|', $value, $matches)) {
      if (count($matches) > 1) {
        foreach ($matches[1] as $this_link) {
          // Surround the tid with '@@' characters so that it can be updated later.
          $value = str_replace('/taxonomy/term/' . $this_link, '/taxonomy/term/@@' . $this_link . '@@', $value);
        }
      }
    }
    return $value;
  }

}
