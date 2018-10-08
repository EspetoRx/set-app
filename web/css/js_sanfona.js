$(".link").click(function(){

  if($(this).hasClass("active")){
    $(".link").removeClass("active");
  }else{
    $(".link").addClass("active");
  }
  
$(".menu").slideUp();
    if ($(this).next().is(":hidden")){
    $(this).next().slideDown(1000,'easeOutBounce');
    }
});
