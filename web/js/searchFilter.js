$(document).ready(function () {
    $("#search").on("keyup", function () {
        var inputValue = this.value;
        if (inputValue.length > 0) {
            $("li", "#w0")
                .hide()
                .filter(function () {
                    //console.log(this);
                    return (
                        removeAccents($(this).text())                        
                                .toLowerCase()
                                .indexOf($("#search").val().toLowerCase()) != -1
                    );
                })
                .show();
        } else {
            $("li", "#w0").show();
        }
    });


    // Remove tildes from text filtering
    const removeAccents = (str) => {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    } 
});
