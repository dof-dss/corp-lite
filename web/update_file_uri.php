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
    $filename = $file->getFileUri();
    if (strpos($filename, '[current-domain:machine-name]') > 0) {
      $newfilename = str_replace('[current-domain:machine-name]', 'historic', $filename);
      $file->setFileUri($newfilename);
      $file->save();
      $n++;
    }
  }
  print("Updated $n file entries");

}

