$(document).ready(function () {
    //console.log('ready ~ script runns');
    $("#search").on("keyup", function () {
        if (this.value.length > 0) {
            //ul#w0.navbar-nav.navbar-left.nav
            //console.log($("li", ".navbar-nav"));
            $("li", "#w0")
                .hide()
                .filter(function () {
                    console.log(this.value);
                    return (
                        $(this)
                            .text()
                            .toLowerCase()
                            .indexOf($("#search").val().toLowerCase()) != -1
                    );
                })
                .show();
        } else {
            $("li", "#w0").show();
        }
    });
});
