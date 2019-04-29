/* global self */

//Task.js se encarga de la creación de tareas desde cualquier lugar de la aplicacion
var AgendaTask = new function () {

    /* Atributos publicos */

    /* end Atributos publicos */

    //Selector de este objeto en el scope de functions
    var self = this;

    //Html renderizado cerca de inputs o contenedores con informacion de usuario
    this.newTaskHtml = "";
    //Selectores para activar el newTaskHtml
    this.newTaskSelectors = "[name*='username'], #username";

    //Constructor
    (function (

            ) {


    })();

    //Init - ejecuta las inicializaciones de scripts del layout
    this.init = function () {

        console.log("Task creator initialized.");
        self.bindStartModal();
        self.findTaskAttributes();

    };

    //bindStartModal - Inicia el modal para crear tareas
    this.bindStartModal = function () {

        $("[data-task='create']").on("click", function (e) {

            e.preventDefault();
            $("#new-task-modal").modal("show");

        });

    };

    //setNewTaskHtml - Setea el html q será renderizado cerca de los inputs para crear tareas
    this.setNewTaskHtml = function (html) {
        self.newTaskHtml = html;
    };

    //findTaskAttributes - Busca en el DOM atributos que puedan ser utiles para la creacion de la tarea
    this.findTaskAttributes = function () {
            
        //Buscamos otros atributos
        var taskAttributes = {
            username:  $("*").find("[data-agenda-username]").val(),
            title:  $("*").find("[data-agenda-title]").val(),
            description:  $("*").find("[data-agenda-description]").val(),
        };

        $(self.newTaskSelectors).each(function (i) {

            //Valor username
            var value = $(this).val();

            //Contenedor
            var container = $(this).parent("div");
            container.append(self.newTaskHtml);
            
            container.attr("data-agenda-username-container", value);
            
            //Set de username
            if (!$(this).attr("data-agenda-username")) {
                $(this).attr("data-agenda-username", value);
            }
                        
            //Evento para agregar tarea
            container.children(".new-task-init").on("click", function () {
                //Seteamos el usuario por username
                $("#new-task-iframe")[0].contentWindow.Task.deleteAllUsers();
                $("#new-task-iframe")[0].contentWindow.Task.setUserByUsername(value);
                $("#new-task-iframe")[0].contentWindow.Task.setTaskAttributes(taskAttributes);
                //Abrimos el modal
                $("#new-task-modal").modal("show");
            });

        });

    };


}