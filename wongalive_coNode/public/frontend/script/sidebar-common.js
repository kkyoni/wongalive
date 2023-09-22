$(document).ready(function(){
    // if ($(window).width() < 767) {
    //     $('.navigation').slimScroll({
    //         //height: 'auto',
    //         height: (($(window).height()) - 227) + 'px',
    //         start: 'top',
    //         width: '100%',
    //         adisableFadeOut: 'true',
    //     });
    // } else {
    //     $('.navigation').slimScroll({
    //         //height: 'auto',
    //         height: (($(window).height()) - 170) + 'px',
    //         start: 'top',
    //         width: '100%',
    //         adisableFadeOut: 'true',
    //     });
    // }
});
$(window).resize(function() {
    // if ($(window).width() < 767) {
    //     $('.navigation').slimScroll({
    //         //height: 'auto',
    //         height: (($(window).height()) - 227) + 'px',
    //         start: 'top',
    //         width: '100%',
    //         adisableFadeOut: 'true',
    //     });
    // } else {
    //     $('.navigation').slimScroll({
    //         //height: 'auto',
    //         height: (($(window).height()) - 170) + 'px',
    //         start: 'top',
    //         width: '100%',
    //         adisableFadeOut: 'true',
    //     });
    // }
});

$(document).ready(function() {
    // if ($(window).width() > 767) {
    //         $('.sidebar').removeClass('active');
    //         $('.content').removeClass('active');
    //     $('.sidebar-close-btn').on('click', function() {
    //         $('.sidebar').addClass('active');
    //         $('.content').addClass('active');
    //     });
    //     $('.sidebar-open-btn').on('click', function() {
    //         $('.sidebar').removeClass('active');
    //         $('.content').removeClass('active');
    //     });
    // } else{
    //     $('.sidebar-close-btn').on('click', function() {
    //         $('.sidebar').removeClass('active');
    //         $('.content').removeClass('active');
    //     });
    //     $('.sidebar-open-btn').on('click', function() {
    //         $('.sidebar').addClass('active');
    //         $('.content').addClass('active');
    //     });
    // }
}); 
$(window).resize(function(){
    // if ($(window).width() > 767) {                    
    //     $('.sidebar-close-btn').on('click', function() {
    //         $('.sidebar').addClass('active');
    //         $('.content').addClass('active');
    //     });
    //     $('.sidebar-open-btn').on('click', function() {
    //         $('.sidebar').removeClass('active');
    //         $('.content').removeClass('active');
    //     });
    // } else{
    //     $('.sidebar-close-btn').on('click', function() {
    //         $('.sidebar').removeClass('active');
    //         $('.content').removeClass('active');
    //     });
    //     $('.sidebar-open-btn').on('click', function() {
    //         $('.sidebar').addClass('active');
    //         $('.content').addClass('active');
    //     });
    // }
});    
    
      $('.submenu').click(function(e) {
       $('.collapse').collapse('hide');
      });
      // $(".sidebar .navigation .submenu").on("mouseleave", function() {
      //       $(this).find(".left-submenu").removeClass("in")
      //   });
  