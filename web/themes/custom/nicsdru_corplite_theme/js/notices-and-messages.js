(a=>{Drupal.behaviors.nicsdruCorpliteNoticesAndMessages={attach:function(s){a(once("nicsdruCorpliteNoticesAndMessages",".info-notice, .messages",s)).each(function(s){var i=a('<strong class="visually-hidden"></strong>'),e="Important information",e=(a(this).hasClass("info-notice--warning")||a(this).hasClass("messages--warning")?e="Warning":a(this).hasClass("info-notice--success")||a(this).hasClass("messages--status")?e="Success":(a(this).hasClass("info-notice--error")||a(this).hasClass("messages--error"))&&(e="Error"),i.text(e+" "),a(this).children("h2:first-child, h3:first-child, h4:first-child, summary"));e.length&&!e.hasClass("visually-hidden")?e.prepend(i):e.length||a(this).prepend(i)})}}})(jQuery);