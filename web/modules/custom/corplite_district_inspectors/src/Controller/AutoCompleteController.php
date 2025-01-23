<?php

namespace Drupal\corplite_district_inspectors\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutoCompleteController extends ControllerBase {
  public function handleAutoComplete(Request $request) {
    $matches = [];
    // Retrieve the string that the user has typed.
    $string = $request->query->get('q');
    if (strlen($string) >= 3) {
      // Fire the autocomplete when they have typed at least 3 chars.
      $matches = $this->getMatches($string);
    }
    return new JsonResponse($matches);
  }

  private function getMatches($string) {
    // Retrieve matches from Solr.
    $index = \Drupal\search_api\Entity\Index::load('school');
    $query = $index->query();
    $query->keys($string);
    $query->setFulltextFields(['name', 'de_reference']);
    $results = $query->execute();
    // How many results have we retrieved ?
    if ($results->getResultCount() > 0) {
      foreach ($results->getResultItems() as $result) {
        $school_name = $result->getField('name')->getValues();
        $de_ref = $result->getField('de_reference')->getValues();
        if (is_numeric($string)) {
          // If the user typed a number, return DE references in the autocomplete.
          $matches[] = [
            'value' => (string)$de_ref[0]
          ];
        } else {
          // If the user typed letters, return school names in the autocomplete.
          $matches[] = [
            'value' => (string)$school_name[0]
          ];
        }
      }
    } else {
      $matches[] = [
        'value' => $this->t("No schools found")
      ];
    }
    return $matches;
  }
}
