<?php
use app\modules\sale\models\BillType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div id="migrar">
    <div class="row">
        <div class="col-md-12">
            <h4>Migracion</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="alicuotaIva">Alicuotas IVA</a>
        </div>
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="condicionIva">Condiciones IVA</a>
        </div>
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="monedas">Monedas y Cotizacion</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="puntoDeVenta">Puntos de Venta</a>
        </div>
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="tipoDeComprobante">Tipos de Comprobante</a>
        </div>
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="tipoDeDocumento">Tipos de Documento</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a class="btn btn-default" href="#" role="button" data-quien="unidadDeMedida">Unidades de Medida</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" id="result">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <label>Tipo de Comprobante:</label><?= Html::dropDownList('tipoComprobante', null, ArrayHelper::map( BillType::find()->all(), "code", "name"), ['id'=>'tipoComprobante']);?>
            <label>Punto de Venta:</label><input name="puntoDeVenta" id="puntoDeVenta"/>
            <label>Numero de comprobante:</label><input name="nroComprobante" id="nroComprobante"/>
            <a class="btn btn-default" href="#" role="button" data-quien="comprobante">Buscar Comprobante</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="result-comprobante">
        </div>
    </div>
</div>
<script>
    var MigracionAfip = new function() {

        this.init = function(){
            $(document).off("click","#migrar a.btn")
                       .on("click","#migrar a.btn", function(){
                    var quien = $(this).data("quien");

                    if (quien=="comprobante") {
                        $.ajax({
                            url: 'index.php?r=invoice/migracionafip/comprobante',
                            data: {
                                tipoComprobante: $("#tipoComprobante").val(),
                                nroComprobante: $("#nroComprobante").val(),
                                puntoDeVenta: $("#puntoDeVenta").val()
                            },
                            method: 'POST',
                            dataType: 'json',
                            success: function (data) {
                                if (data.status != "success" ) {
                                    var errores = "";
                                    $.each(data.errors, function(){
                                        errores += "<p>" + this.code + " - " + this.message + "</p>";
                                    });
                                    $("#result-comprobante").html(errores);
                                } else {

                                    var result = data.data.FECompConsultarResult.ResultGet;
                                    $("#result-comprobante").html(
                                        "<p>Resultado: " + result.Resultado + "</p>" +
                                        "<p>CAE: " + result.CodAutorizacion + "</p>" +
                                        "<p>Fecha Vencimiento: " + result.FchVto + "</p>"
                                    );
                                }
                            }
                        });
                    } else {
                        $.ajax({
                            url: 'index.php?r=invoice/migracionafip/migrar',
                            data: {
                                quien: $(this).data("quien")
                            },
                            method: 'POST',
                            dataType: 'json',
                            success: function (data) {
                                if (data.status != "success") {
                                    $("#result").html(data.errors);
                                } else {
                                    $("#result").html("Migración Exitosa");
                                }
                            }
                        });
                    }
            });
        },
        this.parseJson = function(data, ident)
        {
            ret = "";
            for( var prop in data) {

            }
        }



    }
</script>

<?php  $this->registerJs("MigracionAfip.init();"); ?>