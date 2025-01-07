<?php

namespace Drupal\corplite_district_inspectors\Commands;

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
   */
  public function __construct() {
    parent::__construct();
    $this->entityTypeManager = \Drupal::entityTypeManager();
  }

  /**
   * Drush command to update all school
   * entities to make them available to Solr.
   *
   * @command index-schools
   */
  public function indexSchools() {
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM school");
    $result = $query->fetchAll();
    foreach ($result as $school_row) {
      $school = School::load($school_row->id);
      $school->save();
    }
    $this->io()->write("Schools indexed", TRUE);
  }

}
