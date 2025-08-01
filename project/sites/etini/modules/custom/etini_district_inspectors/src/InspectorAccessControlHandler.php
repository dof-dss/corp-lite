<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the inspector entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class InspectorAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view inspector'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit inspector'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete inspector'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete inspector revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view inspector revision', 'view inspector']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert inspector revision', 'edit inspector']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create inspector', 'administer inspector'], 'OR');
  }

}
