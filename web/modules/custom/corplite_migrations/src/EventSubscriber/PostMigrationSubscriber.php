<?php

namespace Drupal\corplite_migrations\EventSubscriber;

use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Site\Settings;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PostMigrationSubscriber.
 *
 * Post Migrate processes.
 */
class PostMigrationSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\Core\Logger\LoggerChannel definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannel
   */
  protected $logger;

  /**
   * Stores the entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $dbConnD10;

  /**
   * PostMigrationSubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger
   *   Drupal logger.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager,
                              LoggerChannelFactory $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger->get('corplite_migrations');
    $this->dbConnD10 = Database::getConnection('default', 'default');
  }

  /**
   * Get subscribed events.
   *
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::POST_IMPORT][] = ['onMigratePostImport'];
    return $events;
  }

  /**
   * Handle post import migration event.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function onMigratePostImport(MigrateImportEvent $event) {
    $event_id = $event->getMigration()->getBaseId();

    // If we have just migrated site topics then we need to
    // tidy them up afterwards.
    if ($event_id === 'upgrade_d7_taxonomy_term_site_topics') {
      $this->processTaxonomyRefs();
    }
  }

  /**
   * Delete duplicate re-directs.
   */
  protected function processTaxonomyRefs() {
    // Loop through all site topics and look for taxonomy refs.
    $query = $this->dbConnD10->query("SELECT NID FROM {node} where type = 'site_topics'");
    $topics = $query->fetchAll();

    $node_storage = $this->entityTypeManager->getStorage('node');
    foreach ($topics as $topic) {
      // Load up the node.
      $topic_node = $node_storage->load($topic->nid);
      // Look for the pattern '/taxonomy/term/@@nnn@@' in description and summary.
      if (!empty($topic_node->))

      // Load the corresponding alias from the Drupal 10 database.
      $alias_objects = $path_alias_storage->loadByProperties([
        'alias' => '/' . $row->ALIAS
      ]);
      $first_alias = TRUE;
      if (count($alias_objects) > 0) {
        foreach ($alias_objects as $alias) {
          // Check the language code.
          if ($alias->langcode != $row->LANGUAGE) {
            // The language code was incorrect on Drupal 10 so update the
            // corresponding alias with the same language code.
            $this->logger->notice('Updating alias ' . $alias->id() . ', ' . $row->ALIAS . ', to ' . $row->LANGUAGE);
            $alias->langcode = $row->LANGUAGE;
            $alias->save();
          }
          if ($first_alias) {
            $first_alias = FALSE;
          }
          else {
            // We have already processed an alias with this path, so
            // we must delete duplicates.
            $this->logger->notice('Deleting alias ' . $alias->id() . ', ' . $alias->getAlias());
            $path_alias_storage->delete([$alias]);
          }
        }
      }
    }
  }

}
