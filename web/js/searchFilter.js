$(document).ready(function () {
    //console.log('ready ~ script runns');
    $("#search").on("keyup", function () {
        if (this.value.length > 0) {
            $("li")
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
            $("li").show();
        }
    });
});
