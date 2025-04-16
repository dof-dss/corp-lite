<?php

namespace Drupal\corplite_file_verification\Commands;

use Drush\Commands\DrushCommands;
use Drupal\file\Entity\File;
use Drupal\Core\File\FileUrlGeneratorInterface;

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
    foreach ($files as $file) {
      if (!file_exists($file->getFileUri())) {
        $oldfilename = $file->getFileUri();
        $matches = [];
        if (preg_match('/(.*)\.(.*)/', $oldfilename, $matches)) {
          $original_extension = $matches[2];
          // Switch the case of the extension.
          $new_extension = '';
          if ($original_extension == strtoupper($original_extension)) {
            $new_extension = strtolower($original_extension);
          }
          else {
            $new_extension = strtoupper($original_extension);
          }
          $newfilename = str_replace('.' . $original_extension, '.' . $new_extension, $oldfilename);
          // $this->io()->write("New filename is " . $newfilename, TRUE);
          if (file_exists($newfilename)) {
            $this->io()->write("Deleting $newfilename", TRUE);
            $pure_new_filename = str_replace('public://publications/', '', $newfilename);
            exec("rm /var/www/html/web/files/etini/publications/" . $pure_filename);
            $pure_old_filename = str_replace('public://publications/', '', $oldfilename);
            $this->io()->write("Copying down $pure_old_filename", TRUE);
            $rsync_string = 'rsync -azP "$(platform ssh -p xahfhpwqwliq6 -e file-grab-jan25 --pipe)":public_html/sites/etini.gov.uk/files/publications/' . $pure_old_filename . ' /var/www/html/web/files/etini/publications';
            $this->io()->write($rsync_string, TRUE);
            exec($rsync_string);
            break;
          }
        }
      }
    }
  }

}
