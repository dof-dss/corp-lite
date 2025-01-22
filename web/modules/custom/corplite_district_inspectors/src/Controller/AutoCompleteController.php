<?php

namespace Drupal\corplite_district_inspectors\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutoCompleteController extends ControllerBase {
  public function handleAutoComplete(Request $request) {
    $matches = [];
    $string = $request->query->get('q');
    if (strlen($string) >= 3) {
      $index = \Drupal\search_api\Entity\Index::load('school');
      $query = $index->query();
      $query->keys($string);
      $query->setFulltextFields(['name', 'de_reference']);
      $results = $query->execute();
      if ($results->getResultCount() > 0) {
        foreach ($results->getResultItems() as $result) {
          $school_name = $result->getField('name')->getValues();
          $de_ref = $result->getField('de_reference')->getValues();
          $matches[] = [
            'value' => $de_ref[0] . ' - ' . $school_name[0]
          ];
        }
      } else {
        $matches[] = [
          'value' => $this->t("No schools found")
        ];
      }
    }
    return new JsonResponse($matches);
  }
}
