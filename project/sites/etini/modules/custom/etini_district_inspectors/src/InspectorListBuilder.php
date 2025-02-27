<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the inspector entity type.
 */
final class InspectorListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\etini_district_inspectors\InspectorInterface $entity */
    $row['id'] = $entity->id();
    $row['name'] = $entity->get('name')->getString();
    return $row + parent::buildRow($entity);
  }

}
