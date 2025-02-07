/**
 * @file
 * Defines additional Javascript behaviors for Moderation Sidebar (contrib module).
 */
/* eslint-disable */
(function ($, Drupal) {
  Drupal.behaviors.nicsdruCorpliteModerationSidebar = {
    attach: function attach(context) {
      // Add a confirmation dialog to "quick transition" buttons to deal with accidental clicks that could
      // cause something to be published or unpublished.

      var transitionsToConfirm = [
        {
          id: '#quick_publish',
          msg: 'Are you sure you want to publish this content?',
        },

        {
          id: '#publish',
          msg: 'Are you sure you want to publish this content?',
        },

        {
          id: '#unpublish',
          msg: 'Are you sure you want to un-publish this content?',
        },

        {
          id: '#archive',
          msg: 'When archived, this content will be unpublished. Are you sure you want to archive?',
        },

        {
          id: '#restore',
          msg: 'When restored this content will be published. Are you sure you want to restore?',
        },

        {
          id: '#moderation-sidebar-discard-draft',
          msg: 'Any changes made to the draft will be lost.  Are you sure you want to discard this draft?',
        },

      ];

      var dialogOptions = {
        title: Drupal.t('Confirm'),
        resizable: false,
        closeOnEscape: true,
        buttons: [
          {
            text: Drupal.t('Confirm'),
            class: 'button',
            click: function () {
              $(this).dialog('close');
            },
          },
          {
            text: Drupal.t('Cancel'),
            class: 'button button--primary',
            click: function () {
              $(this).dialog('close');
            },
          },
        ],
      };

      $.each(transitionsToConfirm, function (index, transition) {
        $(transition.id, context).click(function (event) {
          //var transitionDialog = Drupal.dialog('<div>' + transition.msg + '</div>', dialogOptions).showModal();
          //return transitionDialog.returnValue;
          return confirm(transition.msg);
        });
      });

    }

  };
})(jQuery, Drupal);
