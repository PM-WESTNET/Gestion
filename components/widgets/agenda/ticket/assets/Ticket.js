//Ticket.js se encarga de creacion y operaciones de tickets
var Ticket = new function () {

    //Usuarios ya seleccionados
    this.alreadySelected = [];

    //URL para la creacion de observaciones
    this.createObservationInputUrl;

    //URL para buscar la duracion por defecto de una categoria
    this.findCategoryDefaultDurationUrl;

    //URL para obtener usuarios por username
    this.getUserByUsernameUrl;

    //URL para obtener información de clientes
    this.getCustomerInfoUrl;

    //URL para obtener categorías segun el tipo de ticket seleccionado
    this.categoriesByTypeUrl;

    this.externalUsers = [];

    //Selector de este objeto en el scope de functions
    var self = this;

    //Constructor
    (function (
            //currentDate
            ) {

        //Current date 
        //this.currentDate = currentDate;

    })();

    //Init - ejecuta las inicializaciones de scripts del layout
    this.init = function () {
        console.log("Ticket manager created");
        this.bindDeleteEvent();
        this.bindDeleteAllUsers();
        this.bindCreateObservation();
        this.bindOnAssignAllusers();
        this.bindGetDefaultDuration();
        this.bindGetCustomerInfo();
        this.bindGetCategoriesByType()
        this.bindCategoryChange();
        $("#category_id").trigger('change')

        //Se previene el submit mas de una vez del formulario
        $('.btn-success').on('click', function (evt) {
            evt.preventDefault();
            $(this).attr('disabled', true);

            $('#ticket-form').submit();
        });

        $('#ticket-form').on('afterValidate', function (evt, msj, attributes) {
            if(attributes.length > 0) {
                $('.btn-success').attr('disabled', false);
            } else {
                $('.btn-success').attr('disabled', true);
            }
        });
    }

    //addUser - Agrega un input hidden con su label para un usuario, verifica tambien que ya no se haya agregado anteriormente
    this.addUser = function (event, ui) {

        event.preventDefault();

        var userId = parseInt(ui.item.id);

        $('#no-users').fadeOut();

        if ($.inArray(userId, self.alreadySelected) < 0) {
            $('#user-list').append(self.createHiddenInput(ui.item.id, ui.item.value, 'default'));
            self.bindDeleteEvent();
            self.alreadySelected.push(userId);
        } else {
            $("#alert-already-selected").slideDown(200);
            setTimeout(function () {
                $("#alert-already-selected").slideUp(200);
            }, 5000);
        }

        $('#user-selection-input').val('');

    }

    //setUser - Setea que usuarios ya han sido asignados para no volver a asignarlos
    this.setUser = function (userId) {
        self.alreadySelected.push(parseInt(userId));
    }

    //setUserByUsername - Setea un usuario segun el username en la tarea
    this.setUserByUsername = function (username) {

        $.ajax({
            url: self.getUserByUsernameUrl,
            dataType: 'json',
            type: 'post',
            data: {
                username: username
            },
            beforeSend: function () {

            }
        }).done(function (response) {
            if (response.status == 'success') {
                //Solo agregamos si el usuario NO esta seleccionado
                if ($.inArray(response.user.id, self.alreadySelected) < 0) {
                    $('#user-list').append(self.createHiddenInput(response.user.id, username, 'default'));
                    self.setUser(response.user.id);
                    self.bindDeleteEvent();
                }
            } else {
                console.log("Ocurrió un error al buscar usuario.");
            }
        }).error(function () {
            console.log("Error en request");
        });

    }

    //setTaskAttributes - Setea atributos de una tarea
    this.setTaskAttributes = function (attributes) {

        $("#task-name").val(attributes.name);
        $("#task-description").text(attributes.description);

    }

    //createHiddenInput - Crea un input hidden y lo devuelve
    this.createHiddenInput = function (userId, userName, statusColor) {

        return '<span id="container-user-' + userId + '" class="label label-' + statusColor + '" style="margin: 10px;">\n\
                    <label for="user-' + userId + '">' + userName + '</label>\n\
                    <input type="hidden" name="Ticket[users][]" value="' + userId + '" id="user-' + userId + '"/>\n\
                    <span data-user="' + userId + '" style="cursor: pointer" class="remove-user glyphicon glyphicon-remove"></span>\n\
                </span>';

    }

    //bindDeleteEvent - Elimina inputs de usuarios cuando se le hace click al icono
    this.bindDeleteEvent = function () {

        $("[data-user]").on("click", function () {

            $(this).unbind("click");

            var inputId = parseInt($(this).attr("data-user"));
            self.alreadySelected.splice($.inArray(inputId, self.alreadySelected), 1);
            $("#container-user-" + inputId).remove();

        });

    }

    //bindDeleteAllUsers - Agrega un evento para eliminar todos los usuarios asignados
    this.bindDeleteAllUsers = function () {

        $("[data-delete-users]").on("click", function () {

            if (confirm("Esto eliminará todos los usuarios asignados. ¿Está seguro?")) {
                $("#user-list").html("");
                self.alreadySelected = [];
            }

        });

    }

    //bindGetCustomerInfo - Fetchs customer information from DB
    this.bindGetCustomerInfo = function () {

        $("#ticket-customer_id").on("change", function () {

            var targetSelector = "data-customer-info-container";
            var $target = $("[" + targetSelector + "]");
            var customerId = $(this).val();

            $.ajax({
                url: self.getCustomerInfoUrl,
                dataType: 'json',
                type: 'get',
                data: {
                    id: customerId,
                },
                beforeSend: function () {
                    $target.text("Buscando información del cliente...");
                }
            }).done(function (response) {
                if (response.status == 'success') {

                    $target.html(response.html);

                } else {
                    console.log("Ocurrió un error al buscar.");
                }
            }).error(function () {
                console.log("Error en request");
            });

        });

    }

    //deleteAllUsers - Dispara evento para borrar todos los usuarios
    this.deleteAllUsers = function () {
        $("#user-list").html("");
        self.alreadySelected = [];
    }

    //setCreateEventInputUrl - Setea la url para crear notas
    this.setCreateObservationInputUrl = function (url) {

        this.createObservationInputUrl = url;

    }

    //setCreateEventInputUrl - Setea la url para crear notas
    this.setCustomerInfoUrl = function (url) {

        this.getCustomerInfoUrl = url;

    }

    //bindCreateEvent - Asigna evento para agregar tareas al boton necesario
    this.bindCreateObservation = function () {

        $("[data-observation='create-observation']").on("click", function (e) {

            e.preventDefault();

            var observationUsername = $(this).attr("data-observation-user");
            var observationTitle = $("[data-observation='observation-title']").val();
            var observationBody = $("[data-observation='observation-body']").val();

            var observation = {
                username: observationUsername,
                title: observationTitle,
                body: observationBody
            };

            if (observation.title != null && observation.body != "") {
                $("[data-observation='observation-title']").val("");
                $("[data-observation='observation-body']").val("");
                self.createObservationInput(observation);
            }

        });

    }
    //createEventInput - Agrega html al form para crear un evento de tipo nota
    this.createObservationInput = function (observation) {

        console.log(observation);

        $.ajax({
            url: self.createObservationInputUrl,
            type: 'post',
            dataType: 'json',
            data: observation
        }).done(function (response) {

            if (response.status == 'success') {

                $("#observation-list > .panel-footer").prepend(response.html);
                self.bindEventDeletion();

            } else {

                console.log("Error");

            }

        }).error(function () {

            console.log("Error en request");

        });

    }

    this.bindGetCategoriesByType = function () {

        $("[data-ticket-type]").on("change", function () {

            var target = $(this).attr("data-ticket-type");
            var typeId = $(this).val();

            if (typeId > 0) {

                $.ajax({
                    url: self.categoriesByTypeUrl,
                    dataType: 'json',
                    type: 'get',
                    data: {
                        id: typeId
                    },
                    beforeSend: function () {

                    }
                }).done(function (response) {
                    if (response.status == 'success') {
                        $(target).html(response.html);
                    } else {
                        console.log("Ocurrió un error al buscar.");
                    }
                }).error(function () {
                    console.log("Error en request");
                });
            }

        });

    }

    //bindDeleteEvent - Asigna evento para eliminar notas agregadas
    this.bindEventDeletion = function () {

        $("[data-observation='delete']").on("click", function () {
            $(this).unbind("click");
            $(this).parents('.event-note').remove();
        });

    }

    //onAssignAllusers - Deshabilita o habilita el input de users segun la seleccion del checkbox "Seleccionar todos los usuarios"
    this.bindOnAssignAllusers = function () {

        $("#task-assignallusers").on("click", function () {

            $("#user-selection-input");

            if ($('#user-selection-input').attr('disabled'))
                $('#user-selection-input').removeAttr('disabled');
            else
                $('#user-selection-input').attr('disabled', 6);


        });

    }

    //bindGetDefaultDuration - Cuando se selecciona una categoria, se actualiza la duracion por defecto de la tareas segun la categoria
    this.bindGetDefaultDuration = function () {

        $("[name='Task[category_id]']").on("change", function () {

            var categoryId = $(this).val();
            var targetSelector = "[name='Task[duration]']";

            $.ajax({
                url: self.findCategoryDefaultDurationUrl,
                dataType: 'json',
                type: 'post',
                data: {
                    id: categoryId
                },
                beforeSend: function () {
                    $(targetSelector).attr("disabled", true);
                }
            }).done(function (response) {
                if (response.status == 'success') {

                    $(targetSelector).val(response.category.default_duration).removeAttr("disabled");

                } else {
                    console.log("Ocurrió un error al buscar.");
                }
            }).error(function () {
                console.log("Error en request");
            });

        });

    }

    //setFindCategoryDefaultDurationUrl - Setea la url para buscar categorias por ajax
    this.setFindCategoryDefaultDurationUrl = function (url) {

        this.findCategoryDefaultDurationUrl = url;

    }

    //setGetUserByUsernameUrl - Setea la url para buscar usuarios por username
    this.setGetUserByUsernameUrl = function (url) {
        this.getUserByUsernameUrl = url;
    }

    //setCategoriesByTypeUrl
    this.setCategoriesByTypeUrl = function (url) {
        this.categoriesByTypeUrl = url;
    }

    this.setExternalUsersUrl = function (url) {
        this.externalUsersUrl = url;
    }

    this.setGetCategoryResponsibleUserUrl = function (url) {
        this.categoryResponsibleUser = url;
    }

    this.bindCategoryChange = function() {
        $(document).off('change', "#category_id").on('change', "#category_id", function(event){
            var external_user_id = $(this).find('option:selected').data('notify');
            if(external_user_id) {
                $('#user-selection').hide();
                //$('#user-selection input').attr('disabled', 1);
                $('#div-mesa-user').show();
                $('#mesa-user').html(self.getExternalUser(external_user_id));
            } else {
                self.setCategoryResponsibleUser(event,$(this).val());
                $('#user-selection').show();
                //$('#user-selection input').removeAttr('disabled');
                $('#div-mesa-user').hide();
            }
        });
    }

    this.setCategoryResponsibleUser = function(event,category_id) {
        $.ajax({
            url: self.categoryResponsibleUser,
            data: {category_id: category_id},
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if(data.status == 'success') {
                    Ticket.addUser(event, data);
                }
            }
        });
    }

    this.getExternalUser = function(id){

        var nombre = '';
        $.each(this.externalUsers, function(key,item) {
            if(item.id==id) {
                nombre = item.nombre;
            }
        });
        return nombre;
    }

    this.loadExternalUsers = function() {
        $.ajax({
            url: self.externalUsersUrl,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                self.externalUsers = data;
            }
        });
    }

}