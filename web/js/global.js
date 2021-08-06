$(document).ready(function () {
  $("table.table").each(function () {
    var cols = $(this).find("thead tr th");
    $(this)
      .find("tbody tr")
      .each(function () {
        $(this)
          .find("td")
          .each(function (i) {
            if (!$(this).data("title")) {
              $(this).attr("data-title", $(cols[i]).text().trim());
            }
          });
      });
  });
  //get body id tag (that is setted by PHP in the server side)
  const bodyID = $('body').attr('id');
  //console.log(bodyID + "~ type: " + typeof bodyID);

  //array with all views to apply filter to
  const specificViewFilters = [
    "user-management-permission-view",
    "user-management-role-view",
    "user-management-user-permission-set",
  ];
  //console.log(specificViewFilters);

  //return true if the current view has the id of a value specified in the previous array
  /* specificViewFilters.some((i) => {
    console.log(`value ${i} is ` + (i === bodyID));
    return i === bodyID;
  }); */

  if (specificViewFilters.some((i) => {
        //console.log(`value ${i} is ` + (i === bodyID));
        return i === bodyID;
    })) {
    //console.log("valid view ~ script running");
    var firstForm = $(".panel-body form").first();
    firstForm.prepend(
      '<div class="row" style="overflow: hidden;"><div class="col-sm-12"><input id="search-in-child-permissions" autofocus="off" type="text" class="form-control input-sm" placeholder="buscar"></div></div><hr/>'
    );

    $("#search-in-child-permissions").on("change keyup", function () {
      //console.log("key pressed");
      var input = $(this);
      //console.log(input.val());

      if (input.val() == "") {
        firstForm.find("label").each(function () {
          $(this).removeClass("hide");
        });
        return;
      }
      //console.log(firstForm.html());

      firstForm.find("label").each(function () {
        var _t = $(this);
        //console.log(_t);
        var labelText = _t[0].innerText;
        //console.log("::::::"+_t.html().indexOf(input.val()));

        if (_t.html().indexOf(input.val()) > -1) {
          _t.closest("label").removeClass("hide");
          _t.closest("label").next().removeClass("hide");
          _t.closest("label").next().next().removeClass("hide");
        } else {
          _t.closest("label").addClass("hide");
          _t.closest("label").next().addClass("hide");
          _t.closest("label").next().next().addClass("hide");
        }
      });
    });
  } else {
    //console.log("body ID doesnt match");
  }
});
