<?php

namespace Drupal\corplite_aliases\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\pathauto\PathautoGenerator;
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
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $pathAliasManager;

  protected $pathautoGenerator;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The entity type manager.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The alias manager.
   */
  public function __construct(EntityTypeManagerInterface $entityManager, AliasManagerInterface $alias_manager, PathautoGenerator $generator) {
    parent::__construct();
    $this->entityTypeManager = $entityManager;
    $this->aliasManager = $alias_manager;
    $this->pathautoGenerator = $generator;
  }

  /**
   * Drush command to force update taxonomy term aliases.
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
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
      $alias = $this->aliasManager->getAliasByPath('/taxonomy/term/'.$tid);
      // If the alias hasn't been set at all then update it.
      if ($alias == '/taxonomy/term/' . $tid) {
        //\Drupal::service('pathauto.generator')->updateEntityAlias($term, 'update', ['force' => TRUE]);
        $this->pathautoGenerator->updateEntityAlias($term, 'update', ['force' => TRUE]);
        $n++;
      }
    } else {
      $entities = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties(['vid' => $option]);
      // Update URL aliases for this vocabulary.
      foreach ($entities as $entity) {
        if (($option) && ($option == $entity->vid->getString())) {
          $this->pathautoGenerator->updateEntityAlias($entity, 'update', ['force' => TRUE]);
          $n++;
        }
      }
    }
    $this->io()->write("Updated $n terms", TRUE);
  }

}
