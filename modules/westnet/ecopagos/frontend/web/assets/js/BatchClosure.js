//BatchClosure.js takes charge on common javascript tasks
var BatchClosure = new function () {

    //Collector information already fetched
    this.alreadyFetchedCollectorInfo = false;

    //Collector information URL
    this.fetchCollectorInfoUrl;

    //Batch closure details URL
    this.fetchPreviewUrl;

    //Self selector
    var self = this;

    //Constructor
    (function (
            //currentDate
            ) {

        //Current date 
        //this.currentDate = currentDate;

    })();

    //Init - Runs configs and startups functions
    this.init = function (newRecord) {

        console.log("Batch closure manager created");

    }

    //bindEnterPressEvent - Binds keypress event for scanning recognition
    this.bindEnterPressEvent = function () {
        //Avoid enter when scan is active
        $("#payout-form").on("keypress", function (e) {

            //If it is an enter keypress
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
            }


        });
    }

    //setFetchCollectorInfoUrl - Sets the url for making ajax requests on a controller that fetches customer information upon requests
    this.setFetchCollectorInfoUrl = function (url) {
        self.fetchCollectorInfoUrl = url;
    }

    //bindPermanentFocus - Sets a permanent state of focus on the customer number input until it is changed with a value
    this.bindPermanentFocus = function () {

        var $number = $("[name='Collector[number]']");
        var $password = $("[name='Collector[password]']");
        var $error = $(".collector-error");
        var $deleteButton = $('[data-batch-closure="delete"]');
        var $previewButton = $("[data-batch-closure='view-details']");
        var $overlay = $("#collector-overlay");

        var afterEnter = function () {

            var $form = $("#form-batch-closure");

            $.ajax({
                url: self.fetchCollectorInfoUrl,
                dataType: 'json',
                type: 'post',
                data: $form.serialize(),
                beforeSend: function () {
                    $error.text("Buscando información del recolector...").removeClass("alert-danger alert-warning").addClass("alert alert-info");
                }

            }).done(function (response) {
                if (response.status == 'success') {

                    $error.html(response.html).removeClass("alert-danger alert-warning alert-info").addClass("bg-primary");
                    $number.attr("readonly", "readonly");
                    $password.attr("readonly", "readonly");
                    $(".to-white").removeClass("font-white");
                    $deleteButton.removeClass("disabled");
                    $previewButton.removeClass("disabled");
                    $overlay.fadeOut();

                } else {
                    $error.text(response.message).addClass("alert alert-warning");
                }

            })/*.error(function () {
                $error.text("Error en los datos del recolector.").addClass("alert alert-danger");
            })*/;
        };

        //Make focus on collector number field
        $number.focus();

        $number.on("keypress", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13 && $number.val() && $password.val()) {
                afterEnter();
            } else {
                $error.text("Complete número y contraseña.").addClass("alert alert-warning");
            }
        });
        $password.on("keypress", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13 && $number.val() && $password.val()) {
                afterEnter();
            } else {
                $error.text("Complete número y contraseña.").addClass("alert alert-warning");
            }
        });

    }

    //setFetchPreviewUrl - Sets the url for making ajax request on a controller that makes a preview for a batch closure
    this.setFetchPreviewUrl = function (url) {
        self.fetchPreviewUrl = url;
    }

    //fetchPreview - Builds a preview for a batch closure
    this.fetchPreview = function () {

        var $previewButton = $("[data-batch-closure='view-details']");
        var $submitButton = $("#batch-closure-submit");
        var $target = $("#batch-closure-preview");
        var $form = $("#form-batch-closure");

        $previewButton.on("click", function () {

            $.ajax({
                url: self.fetchPreviewUrl,
                dataType: 'json',
                type: 'post',
                data: $form.serialize(),
                beforeSend: function () {

                }
            }).done(function (response) {
                if (response.status == 'success') {

                    $target.html(response.html);
                    //$previewButton.attr("disabled", "disabled");
                    $submitButton.removeAttr('disabled');

                } else {
                    $target.html(response.message).addClass("alert alert-warning");
                }
            })/*.error(function () {
                console.log("Error en request");
            })*/;

        })
    }

}