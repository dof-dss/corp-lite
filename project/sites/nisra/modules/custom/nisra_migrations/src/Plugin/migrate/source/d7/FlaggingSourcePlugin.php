<?php

namespace Drupal\nisra_migrations\Plugin\migrate\source\d7;

use Drupal\Core\State\StateInterface;
use Drupal\nisra_migrations\MigrateUuidLookupManager;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * D7 flagging source plugin for Flags module migrations.
 *
 * @MigrateSource(
 *   id = "flagging_source",
 *   source_module = "nisra_migrations",
 * )
 */
class FlaggingSourcePlugin extends SqlBase {

  /**
   * D7 flag ID to filter source data.
   *
   * @var int
   */
  protected $flagId;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);
    $this->flagId = $configuration['flag_id'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('flagging', 'flg')
      ->fields('flg', [
        'flagging_id',
        'fid',
        'entity_id',
        'uid',
      ])
      ->condition('fid', $this->flagId, '=');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'flagging_id' => $this->t('Flagging ID'),
      'fid' => $this->t('Flag ID'),
      'entity_id' => $this->t('Node/Entity ID'),
      'uid' => $this->t('User ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'flagging_id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    if (!parent::prepareRow($row)) {
      return FALSE;
    }

//    // Replace the D7 entity ID with the D9 equivalent.
//    $nids = $this->lookupManager->lookupBySourceNodeId([$row->getSourceProperty('entity_id')]);
//    $row->setSourceProperty('entity_id', $nids[$row->getSourceProperty('entity_id')]['nid']);
    $row->getSourceProperty('entity_id', $row->get('entity_id'));
    var_dump($row);

    return parent::prepareRow($row);
  }

}
