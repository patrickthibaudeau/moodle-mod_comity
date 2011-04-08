/*
 * Settings for FancyBox
 */
try{//try to load fancy settings
$(document).ready(function() {

    $("a#avaliable_rooms_link").fancybox({
        'transitionIn'		: 'none',
        'transitionOut'		: 'none',
        'modal'                 : true,
        'autoDimensions'        : false,
        'width'                 : 600,
        'height'                : 320


    });


});
} catch(err) {
  //no fancy box on this page
  }