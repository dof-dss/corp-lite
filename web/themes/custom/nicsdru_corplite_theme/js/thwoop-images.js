(o=>{Drupal.behaviors.nicsdruCorpliteThwoopImages={attach:function(a){o(once("thwoop-toggle",'[data-picture-mapping*="_expandable"] > img, [data-picture-mapping*="_expandable"] > figure',a)).each(function(){var a=o('<a class="thwooper" aria-label="expand image" href="#"></a>');a.bind("oanimationstart animationstart webkitAnimationStart",function(){o(this).parent().addClass("clearfix")}),a.bind("oanimationend animationend webkitAnimationEnd",function(){o(this).hasClass("thwooped")||o(this).parent().removeClass("clearfix")}),a.click(function(a){a.preventDefault();var a=o(this).find("img, figure"),t=o(this).closest(".media-image"),i=o(this).hasClass("thwooped"),e="expand image"===o(this).attr("aria-lable")?"shrink image":"expand image",a=!i&&a.outerWidth()==t.outerWidth();o(this).toggleClass("thwooped",!i),o(this).attr("aria-label",e),a?(t.addClass("thwooped-modal"),o(this).attr("aria-label","close image")):t.hasClass("thwooped-modal")&&(t.removeClass("thwooped-modal"),t[0].scrollIntoView({block:"center"}))}),o(this).wrap(a)})}}})(jQuery);