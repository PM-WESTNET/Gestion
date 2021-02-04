//Payout.js takes charge on common javascript tasks
var Payout = new function () {

    //Customer information already fetched
    this.alreadyFetchedCustomerInfo = false;

    //Customer information URL
    this.fetchCustomerInfoUrl;

    //Payour reverse URL
    this.payoutReverseUrl;

    //Self selector
    var self = this;

    var isSubmit = false;

    //Constructor
    (function (
        //currentDate
    ) {

        //Current date 
        //this.currentDate = currentDate;

    })();

    //Init - Runs configs and startups functions
    this.init = function (newRecord) {

        Payout.bindEnterPressEvent();
        Payout.bindPermanentFocus();
        $(document).off('click', '#btn-submit')
            .on('click', '#btn-submit', function(){

            if(!Payout.isSubmit && !$(".field-payout-amount").hasClass("has-error")) {
                Payout.bindSubmit();
            }
        });

        if($('.alert').length > 0){
            setTimeout(function(){
                $('.alert').hide(500);
            }, 3000)
        }

    }

    // $(".field-payout-amount").hasClass
    this.bindSubmit = function() {
        var $amount = $('#payout-amount').val();
        if((new Number($amount)) >0) {
            if(confirm('¿ Confirma el cobro de $' + $amount  + ' ?')) {
                Payout.isSubmit = true;
                $('#payout-form').submit();
            }
        }
    }

    //bindEnterPressEvent - Binds keypress event for scanning recognition
    this.bindEnterPressEvent = function () {
        //Avoid enter when scan is active
        $("#payout-form").on("keypress", function (e) {

            //If it is an enter keypress
            var code = e.keyCode || e.which;
            if (code == 13) {
                if(e.target.id == 'payout-amount' && !Payout.isSubmit) {
                    Payout.bindSubmit();
                } else {
                    e.preventDefault();
                }
            } else {
                if( $(".field-payout-amount").hasClass("has-error") && Payout.isSubmit ) {
                    Payout.isSubmit = false;
                }
            }

        });

        //Avoid enter when scan is active
        $("#payout-form").on("keyup", function (e) {

            //If it is an enter keypress
            var code = e.keyCode || e.which;
            if (code != 13) {
                 if( $(".field-payout-amount").hasClass("has-error") && Payout.isSubmit ) {
                    Payout.isSubmit = false;
                }
            }
        });
    }

    //bindPermanentFocus - Sets a permanent state of focus on the customer number input until it is changed with a value
    this.bindPermanentFocus = function () {

        var $target = $("[name*='[customer_number]']");
        var $overlay = $("#customer-overlay");

        //Make focus on customer number field
        $target.focus();
        $target.blur(function () {
            setTimeout(function () {
                if($('.modal.in').length < 1){
                    $target.focus();
                }
            }, 0);
        });

        //Remove permanent focus on enter keypress
        $target.on("keypress", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13 && $target.val()) {

                self.fetchCustomerInfo();

            }
        });

    }

    //setFetchCustomerInfoUrl - Sets the url for making ajax requests on a controller that fetches customer information upon requests
    this.setFetchCustomerInfoUrl = function (url) {
        self.fetchCustomerInfoUrl = url;
    }

    //fetchCustomerInfo - Fetches customer information using an ajax request
    this.fetchCustomerInfo = function () {

        //defines a container which will contain fetched customer information
        var $input = $("[name*='[customer_number]']");
        var $overlay = $("#customer-overlay");
        var $target = $("#data-customer-information");
        var $afterFindTarget = $("[name='Payout[amount]']");
        var $customerInfo = $("#customer-info");
        var $customerMessage = $("#customer-message");

        $.ajax({
            url: self.fetchCustomerInfoUrl,
            dataType: 'json',
            type: 'get',
            data: {
                code: $input.val()
            },
            beforeSend: function () {

            }
        }).done(function (response) {
            if (response.status == 'success') {

                $customerMessage.html("").removeClass('text-center alert alert-danger ');
                $target.html(response.html).removeClass('text-danger');
                $customerInfo.parent().removeClass("col-md-12").addClass("col-md-5");
                $("#barcode-instruction").hide();
                $afterFindTarget.val(response.due);
                $input.unbind("blur");
                $input.attr("readonly", "readonly");
                $(".to-white").removeClass("font-white");
                $overlay.fadeOut();
                $afterFindTarget.focus();
                return true;

            } else {
                $customerMessage.html(response.message).addClass("text-center alert alert-danger");
                return false;
            }
        });

    }

    //setReversePayoutUrl - Sets the URL for making ajax request on a controller that makes the payouts reverses
    this.setReversePayoutUrl = function (url) {
        self.payoutReverseUrl = url;
    }

    //reversePayout - Reverses a payout upon user assert
    this.reversePayout = function () {
        $("[data-payout='reverse-modal'] a[data-payout='reverse-confirm']").on("click", function () {
            var $target = $("#reverse-modal");
        });
    }

    //bindReversePayoutModal - NOT USED
    this.bindReversePayoutModal = function (url) {
        var $starter = $('[data-target="#reverse-ticket-modal"]');
        var $targetInput = $("[name='Payout[customer_number]']");

        $('#reverse-ticket-modal').on('shown.bs.modal', function () {
            self.bindPermanentFocus();
        })

        //Bind a new event for bringing up information
        $targetInput.on("change", function () {

            var value = $(this).val();

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'get',
                data: {
                    number: value
                },
                beforeSend: function () {

                }
            }).done(function (response) {
                if (response.status == 'success') {

                } else {
                    console.log("Ocurrió un error al buscar.");
                }
            }).error(function () {
                console.log("Error en request");
            });

        });

    }

    this.getFunctionByString = function (func, params, obj ) {
        if(obj == undefined) {
            obj = window;
        }
        if(!Array.isArray(func)){
            if(func.indexOf('.')>0) {
                func = func.split('.');
            }
        }
        if(func.length>1) {
            return self.getFunctionByString(func.splice(1), params, obj[func[0]]  );
        } else {
            return obj[func[0]];
        }
    }

    this.printTicket = function(data, callback, chrome_print_app) {
        var ok = false;
        var callback_name = null;
        var callback_function = null;
        var datas = {
            ticket: data
        };

        if(typeof callback === 'function') {
            callback_name = callback.name;
            callback_function = callback;
        } else {
            callback_name = callback;
            callback_function = self.getFunctionByString(callback_name, null);
        }
        $.ajax({
            url: "http://127.0.0.1:4187",
            data: datas,
            method: "GET",
            crossDomain: true,
            dataType: 'jsonp',
            jsonpCallback: callback_name,
            error: function (xhr, ajaxOptions, thrownError){
                if(xhr.status==404) {
                    ok = false;
                }
            },
            done: function(data){
                ok = true;
            }
        });
        if(!ok){
            chrome.runtime.sendMessage(chrome_print_app, datas, callback_function);
        }
    }
}