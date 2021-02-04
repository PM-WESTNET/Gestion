//Agenda.js se encarga del renderizado del calendario y sus operaciones
var Agenda = new function () {

    /* Atributos publicos */
    //Current date 
    this.currentDate = "";

    //Task selector
    this.taskSelector = "";

    //Task quick edit URL
    this.urlQuickEdit = "";

    //Agenda update url
    this.agendaUpdateUrl;

    //Agenda create url
    this.agendaCreateUrl;

    //Modal structure
    this.taskModalSelector = "";
    this.taskIframeSelector = "";

    /* end Atributos publicos */

    //Selector de este objeto en el scope de functions
    var self = this;

    this.constructor = function (
            currentDate,
            taskSelector,
            taskModalSelector,
            taskIframeSelector,
            urlQuickEdit,
            agendaUpdateUrl,
            agendaCreateUrl
            ) {

        //Current date 
        this.currentDate = currentDate;
        //Task selector
        this.taskSelector = taskSelector;

        //Modal structure
        this.taskModalSelector = taskModalSelector;
        this.taskIframeSelector = taskIframeSelector;

        //Agenda update url
        this.agendaUpdateUrl = agendaUpdateUrl;

        //Agenda create url
        this.agendaCreateUrl = agendaCreateUrl;

        //Task quick edit URL
        this.urlQuickEdit = urlQuickEdit;

    }

    //Init - ejecuta las inicializaciones de scripts del layout
    this.init = function () {

        this.startUpdateTaskListener();
        this.insertAddTaskButton();
        this.startFilterFormListener();

    }

    //startUpdateTaskListener - Inicia eventos para click en tareas
    this.startUpdateTaskListener = function () {
        $(self.taskSelector).on("click", function (e) {

            $(this).unbind("click");

            e.preventDefault();

            var url = $(this).attr("href");
            var taskId = url.substr(url.lastIndexOf('=') + 1);
            var iframeUrl = self.urlQuickEdit + '&id=' + taskId;

            $(self.taskIframeSelector).attr("src", iframeUrl);
            $(self.taskModalSelector).modal("show");

            //Al cerrar el modal, recargamos la agenda
            $(self.taskModalSelector).on('hidden.bs.modal', function () {
                location.reload(true);
            })

        });
    }

    //updateAgenda - Actualiza la vista de la agenda
    this.updateAgenda = function () {

        $.ajax({
            url: self.agendaUpdateUrl,
            dataType: 'json',
            type: 'post',
            data: {
                post: 1,
            },
            beforeSend: function () {

            }

        }).done(function (response) {

            if (response.status == 'success') {
                console.log(response);
                $(".agenda").html(response.html);
                Agenda.init();
            }

        }).error(function () {
            console.log("Error en request");
        });

    }

    //insertAddTaskButton - Agrega botones para crear tareas en cada dia
    this.insertAddTaskButton = function () {

        $("td[data-date]:not(.button-already-rendered)").each(function (index, element) {

            var date = $(element).attr("data-date");
            $(element).prepend("<div title='Agregar tarea para este día' class='create-new-task' data-date='" + date + "'><span class='glyphicon glyphicon-plus'></span></div>");
            $(element).addClass("button-already-rendered");
            $(element).children(".create-new-task").on("click", function () {

                var date = $(this).attr("data-date");
                var iframeUrl = self.agendaCreateUrl + '&date=' + date;

                $(self.taskIframeSelector).attr("src", iframeUrl);
                $(self.taskModalSelector).modal("show");

                //Al cerrar el modal, recargamos la agenda
                $(self.taskModalSelector).on('hidden.bs.modal', function () {
                    location.reload(true);
                })

            });
        });

    }

    //startFilterFormListener - Inicia listeners para ver si hay cambios en los inputs del form, para animar el boton de submit
    this.startFilterFormListener = function () {

        $("#search-form input").on("change", function () {

            $("[type='submit']").addClass("btn-primary");

        });

    }

}