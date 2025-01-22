(function ($) {

  "use strict";

  console.log("here we go again");

  Drupal.behaviors.autosubmit = {
    attach: function (context) {
      $('#edit-search-api-fulltext--3').on('autocompleteselect', function (event, data) {
        console.log("been called");
        $('#views-exposed-form-district-inspectors-page-1').submit();
      });
    }
  };
}(jQuery, Drupal));
