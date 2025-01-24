(function ($) {
  "use strict";
  /* Submit the district inspectors form when the user clicks on a typeahead option */
  Drupal.behaviors.autosubmit = {
    attach: function (context) {
      $('input[name="search_api_fulltext"]').on('autocompleteclose', function (event, data) {
        $('#views-exposed-form-district-inspectors-page-1').submit();
      });
    }
  };
}(jQuery, Drupal));
