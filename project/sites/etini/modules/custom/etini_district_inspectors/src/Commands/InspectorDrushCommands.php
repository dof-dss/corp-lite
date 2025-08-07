<?php

namespace Drupal\etini_district_inspectors\Commands;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;
use Drupal\etini_district_inspectors\Entity\School;
use Drupal\etini_district_inspectors\Entity\Inspector;

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
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityTypeManagerInterface $entityManager, Connection $connection) {
    parent::__construct();
    $this->entityTypeManager = $entityManager;
    $this->connection = $connection;
  }

  /**
   * Drush command to update all school
   * entities to make them available to Solr.
   *
   * @command index-schools
   */
  public function indexSchools() {
    $results = $this->entityTypeManager->getStorage('school')->loadMultiple();
    foreach ($results as $school_row) {
      $school = School::load($school_row->id());
      $school->save();
    }
    $this->io()->write(count($results) . " schools indexed", TRUE);
  }

  /**
   * Drush command to import schools and inspectors
   * from the inspector_import table (which has been
   * populated at MySQL level by using
   * 'load data local infile')
   * N.B. This command will destroy all existing data
   * and is only designed to be run once when the
   * inspector_import table has been populated
   *
   * @command import-schools-inspectors
   */
  public function importSchoolsInspectors() {
    // Clear all tables.
    $this->connection->query('truncate table school');
    $this->connection->query('truncate table school_revision');
    $this->connection->query('truncate table inspector');
    $this->connection->query('truncate table inspector_revision');
    // Populate schools and inspectors
    $this->connection->query('insert into school (name, de_reference, phases) select school_name, de_reference, phases from inspector_import');
    $this->connection->query('insert into inspector (name) select distinct(district_inspector) from inspector_import');

    $this->connection->query('update school set inspector_id =
            (select i.id from inspector i join inspector_import ii
             where i.name = ii.district_inspector and ii.school_name = school.name limit 1)');

    $this->connection->query('update inspector set revision_id = id');

    $this->connection->query("insert into inspector_revision (id,revision_id,name, revision_timestamp)
            select id,revision_id,name, '1740664546' from inspector");

    $this->connection->query('update school set revision_id = id');

    $this->connection->query("insert into school_revision (id,revision_id,name,inspector_id,de_reference,phases,revision_timestamp)
            select id,revision_id,name, inspector_id, de_reference,phases, '1740664546' from school");

    $this->io()->write("Schools and Inspectors have been imported, now run 'drush index-schools' to make them available to Solr", TRUE);
  }

}
