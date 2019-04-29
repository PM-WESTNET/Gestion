//Notification.js se encarga del renderizado de notificaciones y ciertas operaciones 
var Notification = new function () {

    /* Atributos publicos */

    this.changeStatusUrl;
    this.batchChangeStatusUrl;

    /* end Atributos publicos */

    //Selector de este objeto en el scope de functions
    var self = this;

    //Constructor
    (function (
            changeStatusUrl,
            batchChangeStatusUrl
            ) {

        this.changeStatusUrl = changeStatusUrl;
        this.batchChangeStatusUrl = batchChangeStatusUrl;

    })();

    //Init - ejecuta las inicializaciones de scripts del layout
    this.init = function () {

        //console.log("Notification manager created.");

        //Oculta la lista de notificaciones onblur
        $(document).mouseup(function (e) {
            var container = $(".notification-list");
            //Si el target no es el contenedor ni un descendiente del contenedor
            if (!container.is(e.target) && container.has(e.target).length === 0)
                container.slideUp();
        });

        //Para mostrar el cuadro de notificaciones
        $("[data-notifications='show']").on("click", function (e) {
            e.preventDefault();
            var $notificationList = $(".notification-list");
            $(".navbar-collapse.collapse.in").removeClass("in");
            if($notificationList.is(":visible"))
                $notificationList.slideUp();
            else
                $notificationList.slideDown();
        });

        //Para cerrar el cuadro de notificaciones
        $("[data-notifications='close']").on("click", function () {
            $(".notification-list").slideToggle();
        });

        //Eventos para cambio de estado de notificaciones
        $(".notification-list [data-status]").on("click", function () {

            var notificationId = $(this).attr("data-id");
            var status = $(this).attr("data-status");
            self.changeStatus(notificationId, status);

        });
        $(".notification-list-grid [data-status]").on("click", function () {

            var notificationId = $(this).attr("data-id");
            var status = $(this).attr("data-status");
            self.changeStatus(notificationId, status);

        });
        
        //Evento para cambiar notificaciones como un grupo hacia leidas
        $("[data-notification='mark-all-as-read']").on("click", function () {
            var notificationIds = {};
            $("[data-notification-status='unread']").each(function (index) {
                var id = $(this).attr("data-notification-container");
                notificationIds[index] = id;
            });
            if (!$.isEmptyObject(notificationIds))
                self.batchChangeStatus(notificationIds, 'read')
        });

    }

    //setChangeStatusUrl - Setea la URL para cambiar el estado de las notificaciones
    this.setChangeStatusUrl = function (url) {
        this.changeStatusUrl = url;
    }
    //setBatchChangeStatusUrl - Setea la URL para cambiar el estado de las notificaciones en grupo
    this.setBatchChangeStatusUrl = function (url) {
        this.batchChangeStatusUrl = url;
    }

    //setNotificationCount - Setea la cantidad de notificaciones en el menú principal
    this.setNotificationCount = function (notificationCount) {
        
        var count = null;

        if (notificationCount > 0) {
            count = parseInt(notificationCount);
        }

        $("[data-notification-count]").text(count);

    }

    //changeStatus - Ajax para cambiar el estado de una notificacion
    this.changeStatus = function (notificationId, status) {

        var data = {
            id: notificationId,
            status: status
        };

        $.ajax({
            url: self.changeStatusUrl,
            type: "post",
            dataType: 'json',
            data: data,
            beforeSend: function () {

            }

        }).done(function (response) {

            if (response.status === 'success') {
                $('[data-notification-container="' + response.notification.notification_id + '"]').attr("data-notification-status", status);
                if (status == 'read')
                    self.updateNotificationCount(-1);
                else
                    self.updateNotificationCount(1);
            } else {
                console.log("Error");
            }

        }).error(function () {

            console.log("Error en request");

        });

    }

    //batchChangeStatus - Cambia el estado de un grupo de notificaciones a partir de sus IDs
    this.batchChangeStatus = function (notificationIds, status) {

        var data = {
            notificationsIds: notificationIds,
            status: status
        };

        $.ajax({
            url: self.batchChangeStatusUrl,
            type: "post",
            dataType: 'json',
            data: data,
            beforeSend: function () {

            }

        }).done(function (response) {

            if (response.status === 'success') {

                console.log(response);

                $.each(response.notifications, function (key, value) {
                    $('[data-notification-container="' + value + '"]').attr("data-notification-status", status);
                });

                self.updateNotificationCount(-1 * $(response.notifications).length);

            } else {
                console.log("Error");
            }

        }).error(function () {

            console.log("Error en request");

        });

    }

    //updateNotificationCount - Selecciona el contador de notificaciones y descuenta dependiendo el valor pasado
    this.updateNotificationCount = function (value) {

        var currentValue = $("[data-notification-count]:first").text();
        
        if (!currentValue)
            currentValue = 0;

        var total = parseInt(currentValue) + value;

        if (total >= 0) {
            self.setNotificationCount(total);
        }

    }


}