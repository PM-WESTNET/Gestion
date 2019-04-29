/**
 * Permite agregar un form (o un div) a una columna de un grid, enviar los datos 
 * del formulario o de los inputs contenidos por el grid a la url especificada
 * por "action" en form o "data-action" en el div, y por ulimo (pero no menos
 * importante), permite actualizar los datos de las filas actualizadas por ajax, 
 * utilizando markup con data-attributes.
 * Ademas permite determinar ciertos eventos que originarian el envio de los 
 * datos al servidor, mediante el uso de marcado (ver Marcado para envio de
 * datos).
 * Para utilizar, se debe generar un form con un action. El controller debe
 * retornar un json:
 * Success:
 * { status: "success", model_id: id, model: {model}, message: "Message" }
 * Error:
 * { status: "error", model_id: id, errors: {model.errors}, model: {...}, 
 * extraData: {...} }
 * Hay dos tipos de marcado para elementos cuyos datos deben ser actualizados:
 *      1. Datos a nivel fila, que corresponden al model.
 *      2. Datos extra, fuera de la fila. No corresponden al model, sino a 
 *      extraData.
 * 1: Las columnas que deben ser actualizadas dentro de la fila, deben ser 
 * marcadas como sigue: data-update="attribute", donde attribute debe ser un 
 * atributo que sera enviado con el model en la respuesta json.
 * 2: Las columnas extra deben ser marcadas como sigue: data-update-extra="datan"
 * donde "datan" es un dato que devuelve el servidor en el objeto extraData.
 * En caso de requerir que un focusout en un input origine que los datos de la 
 * fila sean enviados al servidor, se debe marcar el input con 
 * data-tabupdate="yes".
 * Los errores y mensajes se mostraran en un elemento .help-block colocado
 * dentro de un elemento con el markup data-messages. (Se utiliza este enfoque
 * para reusar los estilos provistos por el framework). Para mostrar los errores
 * de un unico atributo, se debe marcar la clase contenedora del input y del
 * data-messages con [data-attribute="attr"]. Para utilizar un unico espacio
 * para mostrar los errores, no utilizar este ultimo marcado.
 * Marcado para enviar datos:
 * Un input con marcado [data-tabupdate="yes"], iniciara el envio de datos al
 * perder el focus por un tab.
 * Un input con marcado [data-focusout-update], iniciara el envio de datos al
 * perder el focus.
 * Un enter en un input, siempre origina el envio de datos.
 * @type @new;_L4
 */
var ColumnUpdater = new function()
{

    this.init = function(){
        console.log('ColumnUpdater INIT')
        $('form[data-column-updater]').on('submit', function(event){
            event.preventDefault();
            sendForm(this);
        });
        $('[data-column-updater] :input').on('keydown', function(e){
            var keyCode = e.keyCode || e.which; 
            $(this).attr('data-has-changed',1);
            if (keyCode == 13) { 
                $input = $(this);
                submitData($input);
                e.preventDefault();
            } 
        });
        $('div[data-column-updater] :checkbox').on('change', function(e){
            console.log('changed');
            $input = $(this);
                    var action = $input.closest('[data-column-updater]').attr('data-action');
                    var data = $input.closest('[data-column-updater]').find(':input').serialize();
                    sendData(action, data, this);
        });
        $('[data-tabupdate="yes"]').on('keydown', function(e){
            var keyCode = e.keyCode || e.which; 

            if (keyCode == 9) { 
                $input = $(this);
                submitData($input);
            } 
        });
        $('[data-focusout-update]').on('focusout', function(e){
            $input = $(this);
            submitData($input);
        });
        $(window).on('beforeunload',function(){
            $('[data-column-updater]:has([data-has-changed=1])').each(function(i, element){
                $element = $(element);
                if($element.is('form')){
                    $element.submit();
                    }else{
                    var action = $element.attr('data-action');
                    var data = $element.find(':input').serialize();
                        sendData(action, data, this);
                    }
            })
        });
    };
    
    function submitData($input){
            console.trace();
            if($input.val()){
                if($input.closest('[data-column-updater]').is('form')){
                    $input.closest('form').submit();
                }else{
                    var action = $input.closest('[data-column-updater]').attr('data-action');
                    var data = $input.closest('[data-column-updater]').find(':input').serialize();
                    sendData(action, data, $input);
                }
            }
    }
    
    function sendForm(form){
        sendData(form.action, $(form).serialize(), form);
    }

    function sendData(action, data, element){
         console.trace();
        $.ajax({
            url: action,
            data: data,
            type: 'post',
            beforeSend: function(){
                $(element).closest('tr').css('opacity', '0.5');
            }
        }).done(function(response){
            $('tr.success').removeClass('success');

            clearErrors(response.model_id);

            if(response.status == 'success'){
                $('[data-key='+response.model_id+']').addClass('success');
                $('[data-key='+response.model_id+'] :input[type!="hidden"]').val('');
                if(response.message){
                    $('[data-key='+response.model_id+'] [data-messages] .help-block').append(response.message);
                }
                if(response.model){
                    updateData(response.model_id, response.model);
                    if(response.extraData){
                        updateExtraData(response.extraData);
                    }
                }
            }else{
                $('[data-key='+response.model_id+']').addClass('danger');
                $('[data-key='+response.model_id+'] [data-messages]').addClass('has-errors');
                for(err in response.errors){
                    if($('[data-key='+response.model_id+'] [data-messages="'+err+'"]').length > 0){
                        $('[data-key='+response.model_id+'] [data-messages="'+err+'"]').append(response.errors[err]);
                    }else{
                        $('[data-key='+response.model_id+'] [data-messages]').append(response.errors[err]);
                    }
                }
            }
        }).always(function(){
            console. log(element);
            $(element).closest('tr').css('opacity', '1');
            $(element).closest('form').find(':input').attr('data-has-changed',0);
        });
    }

    function clearErrors(row){
        $('[data-key='+row+'] [data-messages]').html('');
        $('[data-key='+row+']').removeClass('danger');
        $('[data-key='+row+'] [data-messages]').removeClass('has-errors');
    }

    function updateData(model_id, model){
        $('[data-key='+model_id+'] [data-update]').each(function(key, value){
            var attr = $(this).attr('data-update');
            $element = $('[data-key='+model_id+'] [data-update="'+attr+'"]');
            if($element.is("input")){
                $element.val(model[attr]).attr('placeholder', model[attr]);
            }else{
                $element.html(model[attr]);
            }
        });
    }

    function updateExtraData(data){
        $('[data-update-extra]').each(function(key, value){
            var attr = $(this).attr('data-update-extra');
            $element = $('[data-update-extra="'+attr+'"]');
            if($element.is("input")){
                $element.val(data[attr]).attr('placeholder', data[attr]);
            }else{
                $element.html(data[attr]);
            }
        });
    }
}

