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
    $query = $this->dbConnD10->query("SELECT nid FROM {node} where type = 'site_topics'");
    $topics = $query->fetchAll();
    $node_storage = $this->entityTypeManager->getStorage('node');
    foreach ($topics as $topic) {
      // Load up the node.
      $topic_node = $node_storage->load($topic->nid);
      // Process fields that may contain taxonomy links.
      $topic_node = $this->processField('field_description', $topic_node);
      $topic_node = $this->processField('field_topic_summary', $topic_node);
      // Save the node.
      $topic_node->save();
    }
  }

  /**
   * Process links in specified field.
   */
  protected function processField($field, $node) {
    $field_text = $node->get($field)->getString();
    if (!empty($field_text)) {
      $matches = [];
      // Look for the pattern '/taxonomy/term/@@nnn@@' in the text.
      if (preg_match_all('/@@([[:digit:]]+)@@/', $field_text, $matches)) {
        if (count($matches) > 1) {
          foreach ($matches[1] as $tid) {
            // Get a list of all migrate map tables and search through them for this tid.
            $map_query = $this->dbConnD10->query("select table_name from information_schema.tables where table_name like 'migrate_map_upgrade_d7_taxonomy_term%' and table_schema = database()");
            $map_tables = $map_query->fetchAll();
            foreach ($map_tables as $map_table) {
              $node = $this->processMapTable($map_table, $field, $field_text, $tid, $node);
            }
          }
        }
      }
    }
    return $node;
  }

  /**
   * Process map table.
   */
  protected function processMapTable($map_table, $field, $field_text, $tid, $node) {
    $map_table_name = $map_table->table_name;
    $tid_query = $this->dbConnD10->query("select destid1 from $map_table_name where sourceid1 = $tid");
    $new_tids = $tid_query->fetchAll();
    if (count($new_tids) > 0) {
      foreach ($new_tids as $new_tid) {
        $ntid = $new_tid->destid1;
        if (preg_match('/site_topics/', $map_table_name)) {
          // Link now points to a site topic node.
          $field_text = str_replace("/taxonomy/term/@@$tid@@", "/node/$ntid", $field_text);
        } else {
          // Link is still a taxonomy ref.
          $field_text = str_replace("@@$tid@@", $ntid, $field_text);
        }
        $node->set($field, $field_text);
      }
    }
    return $node;
  }

}
