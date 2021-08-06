$(document).ready(function () {
    const bodyID = $('body').attr('id');

    const specificViewFilters = [
        "user-management-permission-view",
        "user-management-role-view",
        "user-management-user-permission-set",
    ];

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
            var input = $(this);

            firstForm.find("label").each(function () {
                var _t = $(this);
                var labelText = _t[0].innerText;

                if (_t.html().indexOf(input.val()) > -1) {
                    //console.log(input.val());
                    _t.closest("label").show();
                    _t.closest("label").next().show();
                    _t.closest("label").next().next().show();
                } else {
                    _t.closest("label").hide();
                    _t.closest("label").next().hide();
                    _t.closest("label").next().next().hide();
                }
            });
        });
    } else {
        //console.log("body ID doesnt match");
    }
});