<?php

use Drupal\Core\DrupalKernel;
use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Database;
use Drupal\etini_district_inspectors\Entity\School;

////////////////////////////
// Start Drupal bootstrap //
////////////////////////////
$autoloader = require_once 'autoload.php';

$errors = [];
$request = Request::createFromGlobals();

$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
try {
  $kernel->boot();
  $kernel->preHandle($request);
} catch (Exception $e) {
  $message = preg_replace('/\'(.*)\'(@\'.*\')/m', '\'*****\'$2', $e->getMessage());
  $errors[] = 'Unable to boot the kernel: ' . $message;
}

////////////////////////////
// End Drupal bootstrap //
////////////////////////////

// If there are no errors, run some test code.
if (empty($errors)) {

  // Start test code here
  $file_storage = \Drupal::entityTypeManager()->getStorage('file');
  $fids = \Drupal::entityQuery('file')
    ->accessCheck(FALSE)
    ->execute();
  $files = $file_storage->loadMultiple($fids);
  $n = 0;
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
        if (file_exists($newfilename)) {
          $pure_new_filename = str_replace('public://publications/', '', $newfilename);
          print("rm -f /Users/martindutton/apps/corp-lite/web/files/etini/publications/" . $pure_new_filename . "<br/>");
          $pure_old_filename = str_replace('public://publications/', '', $oldfilename);
          $rsync_string = 'rsync -azP "$(platform ssh -p xahfhpwqwliq6 -e file-grab-jan25 --pipe)":public_html/sites/etini.gov.uk/files/publications/' . $pure_old_filename . ' /Users/martindutton/apps/corp-lite/web/files/etini/publications/';
          print($rsync_string . "<br/>");
        }
      }
    }
  }
  print("Updated $n file entries");

}

