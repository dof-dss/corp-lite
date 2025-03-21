<?php

namespace Drupal\corplite_revision_delete\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Drush custom commands.
 */
class RevisionDeleteDrushCommands extends DrushCommands {

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
   * Drush command to delete all revisions before current published revision.
   *
   * @command revision-delete
   */
  public function RevisionDelete(string $option) {
    $n = 0;
    if (empty($option)) {
      $this->io()->write("Please specify a content type to delete revisions on", TRUE);
      return;
    }
    // Get the content type of content revisions that need to be deleted.
    $nodes = $this->entityTypeManager
      ->getStorage('node')
      ->loadByProperties(['type' => $option]);

    // Loop through all the nodes and get their revisions.
    foreach ($nodes as $nid) {
      $node_storage = $this->entityTypeManager
        ->getStorage('node');

      /** @var \Drupal\node\NodeInterface $nid */
      $revisions = $node_storage->revisionIds($nid);

      // Get the published revision if there is one.
      $latest_revision = $nid->getLoadedRevisionId();

      // Only delete revisions on nodes that have a published version.
      if ($nid->isPublished()) {
        foreach ($revisions as $revision) {
          // If the revision number is less than the published revision then delete it. Keep everything after the published version.
          if ($revision < $latest_revision) {
            $node_storage->deleteRevision($revision);
            $n++;
          }
        }
      }
    }
    $this->io()->write("Deleted $n revisions", TRUE);
  }

}
