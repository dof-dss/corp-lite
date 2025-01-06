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
   * Drush command to update all school and inspector
   * entities to make them available to Solr.
   *
   * @command index-inspectors
   */
  public function indexInspectors() {
    $this->io()->write("About to run query", TRUE);
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM school");
    $result = $query->fetchAll();
    foreach ($result as $school_row) {
      $this->io()->write("School id is " . $school_row->id, TRUE);
      $school = School::load($school_row->id);
      //$this->io()->write("Revision created " . $school->get('revision_created')->getString(), TRUE);
      $school->save();
    }
    $query = $database->query("SELECT * FROM inspector");
    $result = $query->fetchAll();
    foreach ($result as $inspector_row) {
      $this->io()->write("Inspector id is " . $inspector_row->id, TRUE);
      $inspector = Inspector::load($inspector_row->id);
      //$this->io()->write("Revision created " . $school->get('revision_created')->getString(), TRUE);
      $inspector->save();
    }
    $this->io()->write("Successfully ran query", TRUE);
  }

}
