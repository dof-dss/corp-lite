<?php

namespace Drupal\corplite_file_verification\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush custom commands.
 */
class CorpLiteDrushCommands extends DrushCommands {

  /**
   * Core EntityTypeManager instance.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->entityTypeManager = \Drupal::entityTypeManager();
  }

  /**
   * Drush command to fix broken links to files.
   *
   * @command verify-file-links
   */
  public function fixBrokenFiles() {
    $file_storage = $this->entityTypeManager->getStorage('file');
    $fids = \Drupal::entityQuery('file')
      ->accessCheck(FALSE)
      ->execute();
    $files = $file_storage->loadMultiple($fids);
    //$this->io()->write("Going in to foreach loop", TRUE);
    foreach ($files as $file) {
      //$this->io()->write("In foreach loop", TRUE);
      if (!file_exists($file->getFileUri())) {
        //$this->io()->write($file->getFileUri(), TRUE);
        $oldfilename = $file->getFileUri();
        $matches = [];
        if (preg_match('/(.*)\.(.*)/', $oldfilename, $matches)) {
          //$this->io()->write(print_r($matches, true), TRUE);
          $original_extension = $matches[2];
          // Switch the case of the extension.
          $new_extension = '';
          if ($original_extension == strtoupper($original_extension)) {
            $new_extension = strtolower($original_extension);
          } else {
            $new_extension = strtoupper($original_extension);
          }
          $newfilename = str_replace('.' . $original_extension, '.' . $new_extension, $oldfilename);
          //$this->io()->write("New filename is " . $newfilename, TRUE);
          if (file_exists($newfilename)) {
            $this->io()->write($newfilename, TRUE);
          }
        }
      }
    }
  }
}
