(t=>{Drupal.behaviors.nicsdruCorpliteRemoveEmptyTags={attach:function(e){t(once("emptyTags","p",e)).filter(function(){return""===t.trim(t(this).html().replace(/&nbsp;/g,""))}).remove()}}})(jQuery,once);