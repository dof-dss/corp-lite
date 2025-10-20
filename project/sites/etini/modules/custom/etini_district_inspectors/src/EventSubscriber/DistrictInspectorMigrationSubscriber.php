<?php

namespace Drupal\etini_district_inspectors\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateRollbackEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DistrictInspectorMigrationSubscriber.
 *
 * Deeletes ETI District Inspectors after migration has been rolled back.
 *
 * @package Drupal\etini_district_inspectors
 */
class DistrictInspectorMigrationSubscriber implements EventSubscriberInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Get subscribed events.
   *
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_ROLLBACK][] = ['postRollback'];
    return $events;
  }

  /**
   * Deelete inspectors after rolling back migration.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function postRollback(MigrateRollbackEvent $event): void {
    // Delete inspectors that aren't referenced.
    $this->connection->query('delete from inspector where not exists (select school.inspector_id from school where school.inspector_id = inspector.id)');
  }
}
