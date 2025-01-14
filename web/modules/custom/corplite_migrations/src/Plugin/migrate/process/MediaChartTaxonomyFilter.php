<?php

namespace Drupal\corplite_migrations\Plugin\migrate\process;

use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * Processes /\[\[chart-nid:(\d+),chart-view-mode:full]]/ tokens in content.
 *
 * These style tokens come from chart embed module. The regex it uses to match
 * them for reference is:
 *
 * /\[\[chart-nid:(\d+),chart-view-mode:full]]/
 *
 * @code
 * # From this
 * [[chart-nid:1751,chart-view-mode:full]]
 *
 * # To this
 * <drupal-entity
 *   data-entity-type="node"
 *   data-entity-uuid="2fdf6f0c-ac41-4d24-a491-06417a2a6c80"
 *   data-embed-button="chart"
 *   data-entity-embed-display="view_mode:node.easychart"
 *   data-langcode="en"
 *   data-entity-embed-display-settings="[]">
 *  </drupal-entity>
 * @endcode
 *
 * Usage:
 *
 * @endcode
 * process:
 *   bar:
 *     plugin: media_chart_taxonomy_filter
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "media_chart_taxonomy_filter"
 * )
 */
class MediaChartTaxonomyFilter extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a UpdateFileToDocument process plugin instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, Connection $connection) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return $value;
    }

    $pattern = '/\[\[chart-nid:(\d+),chart-view-mode:full]]/';

    $messenger = $this->messenger();
    $tid = $row->getSourceProperty('tid');
    $value = preg_replace_callback($pattern, function($matches) use ($messenger, $tid) {
      $replacement_template = <<<'TEMPLATE'
<drupal-entity
data-entity-type="node"
data-entity-uuid="%s"
data-embed-button="chart"
data-entity-embed-display="view_mode:node.easychart"
data-langcode="en"
data-entity-embed-display-settings="[]">
</drupal-entity>
TEMPLATE;

      // Ensure we have a managed file for the embedded asset.
      $query = $this->connection->select('node', 'n');
      $query->condition('n.nid', $matches[1], '=');
      $query->fields('n', ['uuid']);
      $query->range(0, 1);
      $node = $query->execute()->fetchAssoc();

      return sprintf($replacement_template, $node['uuid']);
    }, $value);

    return $value;
  }
}
