(function ($) {
  "use strict";
  Drupal.behaviors.autosubmit = {
    attach: function (context) {
      $('#edit-search-api-fulltext--3').on('autocompleteclose', function (event, data) {
        $('#views-exposed-form-district-inspectors-page-1').submit();
      });
    }
  };
}(jQuery, Drupal));
