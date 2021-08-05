$(document).ready(function () {
    //console.log('ready ~ script runns');
    $("#search").on("keyup", function () {
        if (this.value.length > 0) {
            $("li", ".navbar-nav").not(".not-filter") //terminar de implementar el filtro
                .hide()
                .filter(function () {
                    return (
                        $(this)
                            .text()
                            .toLowerCase()
                            .indexOf($("#search").val().toLowerCase()) != -1
                    );
                })
                .show();
        } else {
            $("li", ".navbar-nav").not(".not-filter").show();
        }
    });
});
