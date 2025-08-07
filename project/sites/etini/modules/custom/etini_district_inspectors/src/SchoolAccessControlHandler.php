<?php

declare(strict_types=1);

namespace Drupal\etini_district_inspectors;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the school entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class SchoolAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view school'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit school'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete school'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete school revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view school revision', 'view school']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert school revision', 'edit school']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create school', 'administer school'], 'OR');
  }

}
