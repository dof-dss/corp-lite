<?php

namespace Drupal\corplite_district_inspectors\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;
use Drupal\corplite_district_inspectors\Entity\School;
use Drupal\corplite_district_inspectors\Entity\Inspector;

/**
 * Drush custom commands.
 */
class InspectorDrushCommands extends DrushCommands {

  /**
   * Core EntityTypeManager instance.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityManager) {
    parent::__construct();
    $this->entityTypeManager = $entityManager;
  }

  /**
   * Drush command to update all school
   * entities to make them available to Solr.
   *
   * @command index-schools
   */
  public function indexSchools() {
    $results = $this->entityTypeManager->getStorage('School')->loadMultiple();
    foreach ($results as $school_row) {
      $school = School::load($school_row->id());
      $school->save();
    }
    $this->io()->write(count($results) . " schools indexed", TRUE);
  }

}
