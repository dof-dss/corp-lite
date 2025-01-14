<?php

namespace Drupal\corplite_aliases\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Drush custom commands.
 */
class AliasDrushCommands extends DrushCommands {

  /**
   * Core EntityTypeManager instance.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityManager) {
    parent::__construct();
    $this->entityTypeManager = $entityManager;
  }

  /**
   * Drush command to update all school
   * entities to make them available to Solr.
   *
   * @command alias-taxonomy-terms
   */
  public function aliasTaxonomyTerms(string $option = NULL) {
    $n = 0;
    if (empty($option)) {
      $this->io()->write("Please specify 'all', or a vocabulary name, or a tid", TRUE);
      return;
    }
    if (is_numeric($option)) {
      $tid = $option;
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
      $aliasManager = \Drupal::service('path_alias.manager');
      $alias = $aliasManager->getAliasByPath('/taxonomy/term/'.$tid);
      if ($alias == '/taxonomy/term/' . $tid) {
        \Drupal::service('pathauto.generator')->updateEntityAlias($term, 'update', ['force' => TRUE]);
        $n++;
      }
    } else {
      $result = \Drupal::entityQuery('taxonomy_term')->accessCheck(FALSE)->execute();
      $entities = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($result);
      $vid = NULL;
      if ($option != 'all') {
        $vid = $option;
      }
      // Update URL aliases.
      foreach ($entities as $entity) {
        if (($vid) && ($vid == $entity->vid->getString())) {
          \Drupal::service('pathauto.generator')->updateEntityAlias($entity, 'update', ['force' => TRUE]);
          $n++;
        }
      }
    }
    $this->io()->write("Updated $n terms", TRUE);
  }

}
