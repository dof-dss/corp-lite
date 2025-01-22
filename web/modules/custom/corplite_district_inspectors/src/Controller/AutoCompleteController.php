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
      $matches[] = [
        'value' => '',
        'label' => $this->t("No schools found")
      ];
    }
    return new JsonResponse($matches);
  }
}
