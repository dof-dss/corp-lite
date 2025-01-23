<?php

namespace Drupal\corplite_district_inspectors\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\search_api\Entity\Index;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutoCompleteController extends ControllerBase {

  /**
   * Handle autocomplete.
   */
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

  /**
   * Retrieve matches from Search API.
   */
  private function getMatches($string) {
    $index = Index::load('school');
    $query = $index->query();
    $query->keys($string);
    $query->setFulltextFields(['name', 'de_reference']);
    $results = $query->execute();
    // How many results have we retrieved ?
    if ($results->getResultCount() > 0) {
      foreach ($results->getResultItems() as $result) {
        // Display DE ref and school name in typeahead.
        $school_name = $result->getField('name')->getValues();
        $de_ref = $result->getField('de_reference')->getValues();
        $matches[] = [
          'value' => $de_ref[0] . ' - ' . $school_name[0]
        ];
      }
    }
    else {
      $matches[] = [
        'value' => $this->t("No schools found")
      ];
    }
    return $matches;
  }

}
