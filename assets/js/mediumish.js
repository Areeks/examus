$(function(){
  var topOfOthDiv = $(".hideshare").offset().top;
  $(window).scroll(function() {
      if($(window).scrollTop() > topOfOthDiv) { 
          $(".share").hide();
      }
      else{
        $(".share").show();
      }
  });
});
