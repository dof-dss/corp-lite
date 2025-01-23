(function ($) {
  "use strict";
  Drupal.behaviors.autosubmit = {
    attach: function (context) {
      $('input[name="search_api_fulltext"]').on('autocompleteclose', function (event, data) {
        $('#views-exposed-form-district-inspectors-page-1').submit();
      });
    }
  };
}(jQuery, Drupal));
