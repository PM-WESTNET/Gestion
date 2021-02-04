//Task.js se encarga de creacion y operaciones de tareas
var Task = new function () {

    //Usuarios ya seleccionados
    this.alreadySelected = [];

    //URL para la creacion de notas
    this.createEventInputUrl;

    //URL para buscar la duracion por defecto de una categoria
    this.findCategoryDefaultDurationUrl;

    //URL para obtener usuarios por username
    this.getUserByUsernameUrl;

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
        console.log("Task manager created");
        this.bindDeleteEvent();
        this.bindDeleteAllUsers();
        this.bindCreateEvent();
        this.bindOnAssignAllusers();
        this.bindGetDefaultDuration();
    }

    //createEventInput - Agrega html al form para crear un evento de tipo nota
    this.createEventInput = function (taskEvent) {

        $.ajax({
            url: self.setCreateEventInputUrl,
            type: 'post',
            dataType: 'json',
            data: taskEvent
        }).done(function (response) {

            if (response.status == 'success') {

                $("#event-list > .panel-footer").prepend(response.html);
                self.bindEventDeletion();

            } else {

                console.log("Error");

            }

        }).error(function () {

            console.log("Error en request");

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
                    <input type="hidden" name="Task[users][]" value="' + userId + '" id="user-' + userId + '"/>\n\
                    <span data-user="' + userId + '" style="cursor: pointer" class="remove-user glyphicon glyphicon-remove"></span>\n\
                </span>';

    }

    //disableUserInput - Habilita el input para seleccion de usuarios dependiendo el tipo de tarea que se seleccione
    this.disableUserInput = function (value) {
        /*
         $("#task-task_type_id").on("change", function () {
         if (parseInt(value) === parseInt($(this).val())) {
         $("#user-selection").slideUp(200);
         } else {
         $("#user-selection").slideDown(200);
         }
         });
         */
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

    //deleteAllUsers - Dispara evento para borrar todos los usuarios
    this.deleteAllUsers = function () {
        $("#user-list").html("");
        self.alreadySelected = [];
    }

    //setCreateEventInputUrl - Setea la url para crear notas
    this.setCreateEventInputUrl = function (url) {

        this.setCreateEventInputUrl = url;

    }

    //bindCreateEvent - Asigna evento para agregar tareas al boton necesario
    this.bindCreateEvent = function () {

        $("[data-event='create-event']").on("click", function (e) {

            e.preventDefault();

            var eventType = $(this).attr("data-event-type");
            var eventUsername = $(this).attr("data-event-user");
            var eventBody = $("[data-event='event-body']").val();

            var taskEvent = {
                type: eventType,
                username: eventUsername,
                body: eventBody
            };

            if (taskEvent.type != null && taskEvent.body != "") {
                $("[data-event='event-body']").val("");
                self.createEventInput(taskEvent);
            }

        });

    }

    //bindDeleteEvent - Asigna evento para eliminar notas agregadas
    this.bindEventDeletion = function () {

        $("[data-event='delete']").on("click", function () {
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

}