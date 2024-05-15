<?php

namespace Drupal\corplite_migrations\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'SiteTopicReference' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "site_topic_reference"
 * )
 */
class SiteTopicReference extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new SiteTopicReference object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $conn = Database::getConnection('default', 'default');
    // Get the nid of the node currently being migrated.
    $current_migration_node_nid = $row->get('nid');
    // Loop through the topics in the field_site_topics field.
    foreach ($row->get('field_site_topics') as $topic) {
      $d7_tid = $topic['tid'];
      // Look up this D7 site topic tid to see which site topic nid it maps to on Drupal 10.
      $tid_query = $conn->query("select destid1 from migrate_map_upgrade_d7_taxonomy_term_site_topics where sourceid1 = $d7_tid");
      $new_tids = $tid_query->fetchAll();
      if (count($new_tids) > 0) {
        foreach ($new_tids as $new_tid) {
          $topic_nid = $new_tid->destid1;
          // Load up the site topic node.
          $node = $this->entityTypeManager->getStorage('node')->load($topic_nid);
          if (!empty($node)) {
            // Add this nid of the node being migrated to the list of nids referenced by this topic (if it isn't already there).
            $found = FALSE;
            foreach ($node->field_topic_content as $topic) {
              $nid = $topic->getString();
              if ($nid == $current_migration_node_nid) {
                $found = TRUE;
              }
            }
            if (!$found) {
              $node->field_topic_content->appendItem(['target_id' => $current_migration_node_nid]);
              $node->save();
            }
          }
        }
      }
    }
    return $value;
  }

}
