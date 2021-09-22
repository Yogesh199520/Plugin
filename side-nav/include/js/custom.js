jQuery('.dropdown-menu').click(function() {
  //jQuery('.sub-menu-1').toggleClass('child-menu-item');
  jQuery(this).parents("li").children(".sub-menu-1").toggleClass('child-menu-item');
});

jQuery('.dropdown-dropdown-menu').click(function() {
  jQuery(this).parents("li").children(".sub-menu-2").toggleClass('child-menu-item');
});

jQuery('.dropdown-menu-level2').click(function() {
  jQuery(this).parents("li").children(".sub-menu-3").toggleClass('child-menu-item');
});

jQuery(".dropdown-menu").click(function(){
 //jQuery('.icon-placement-level-zero').toggleClass('fa fa-plus fa fa-minus');
 jQuery(this).children(".icon-placement-level-zero").toggleClass('fa fa-plus fa fa-minus');
})

jQuery(".dropdown-dropdown-menu").click(function(){
 jQuery(this).children(".icon-placement-level-one").toggleClass('fa fa-plus fa fa-minus');
})

jQuery(".dropdown-menu-level2").click(function(){
 jQuery(this).children(".icon-placement-level-two").toggleClass('fa fa-plus fa fa-minus');
})

