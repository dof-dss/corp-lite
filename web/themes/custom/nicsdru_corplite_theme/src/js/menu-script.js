/* eslint-disable */
(function ($, Drupal) {
  Drupal.behaviors.nicsdruUnityMainMenu = {
    attach: function (context, settings) {
      $(once('has-submenu', '#block-mainnavigation')).find('.expanded .nav-link').each(function () {

        var $submenu = $(this).next('.nav-main_sub');

        if ($submenu.length == 1) {
          var $content = $(this).text();
          var $link = $(this).attr("href");

          // If menu item has link then duplicate link as a sub menu item.
          if ($link) {
            $submenu.prepend('<li class="nav-item leaf title"><a href="' + $link + '" class="nav-link">' + $content + '</a></li>')
          }

          $(this).attr('aria-haspopup', 'true');
          $(this).attr('aria-expanded', 'false');
          $(this).addClass('menu-toggle-btn');
          $(this).attr("tabindex", "0");
          $(this).removeAttr('href');
          $(this).append('<svg aria-hidden="true" class="ico ico-arrow-down"><use xlink:href="#arrow"></use></svg>');
        }
      });

      var clickHandler = function (e) {
        if ((e.type == 'keypress') && (e.which != 13)) {
          return;
        } else {
          e.preventDefault();

          $(this).attr('aria-expanded', function (i, attr) {
            return attr == 'true' ? 'false' : 'true'
          });
          $(this).parent('.expanded').toggleClass('open');

          var $sibling = $(this).parent('.expanded').siblings();

          $sibling.removeClass('open');
          $sibling.find('.expanded').removeClass('open');
          $sibling.find('.nav-link').attr('aria-expanded', 'false');
        }
      };

      $(once('open-submenu', '#block-mainnavigation .menu-toggle-btn')).on('click keypress', clickHandler);
    }
  }

  var escHandler = function (event) {
    if ((event.type == 'keydown') && (event.which != 27)) {
      return;
    } else {
      event.preventDefault();

      var activeElement = document.activeElement;
      var subLinkParent = $(activeElement).parents('.nav-main_sub');

      if (subLinkParent.length) {
        $(subLinkParent).siblings('a').focus();
      }

      closeMenu();
    }
  }
  $(once('esc-close-submenu', document)).on('keydown', escHandler);

  $(once('close-submenu', document)).click(function (event) {
    var $trigger = $('#block-mainnavigation');
    if ($trigger !== event.target && !$trigger.has(event.target).length) {
      closeMenu();
    }
  });

  var closeMenu = function () {
    console.log('Close Menu')
    $('#block-mainnavigation .menu-toggle-btn')
      .attr('aria-expanded', 'false')
      .parent('.expanded').removeClass('open')
  }
})(jQuery, Drupal);
