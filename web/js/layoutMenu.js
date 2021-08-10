$(document).ready(function() {
    //alert('layoutMenu.js');

    var uLists = $("#main-menu").find("ul");
  
    var dividersSet = uLists.find("li.divider");
    dividersSet.each(function(i){
      var liDivider = $(this);
      //console.log(liDivider);
      while(liDivider.next().is("li.divider")){
        liDivider.next().remove();
      }
    });

    var liDropDown = $("#main-menu").find("li.dropdown");
    
    //console.log(liDivider);
    liDropDown.each(function() {
        $(this).children("ul").each(function() {

            // remove any excess dividers that were hardcoded 
            if($(this).children().last().is("li.divider")){
                $(this).children().last().remove();
              }
            if($(this).children().first().is("li.divider")){
                $(this).children().first().remove();
            }
        });
    });

    
});
