<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 14/05/18
 * Time: 15:24
 */
use dosamigos\ckeditor\CKEditor;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
?>

<div class="mobile-push">

    <?= $form->field($model, 'subject')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'content')->widget(CKEditor::class, [
        'options' => ['rows' => 6, 'id' => 'content'],
        'clientOptions' => [
            'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
        ]
    ]); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= Yii::t('app', 'References') ?>:
        </div>
        <div class="col-sm-9">
            <span class="reference label label-default" data-ref="@Nombre" id="lbl-nombre">@Nombre</span>
            <span class="reference label label-warning" data-ref="@PaymentCode" id="lbl-codigo-de-pago">@CodigoDePago</span>
            <span class="reference label label-danger" data-ref="@FacturasAdeudadas" id="lbl-facturas-adeudadas">@FacturasAdeudadas</span>
            <span class="reference label label-warning" data-ref="@ValorDeExtensionDePago" id="lbl-valor-extension-de-pago">@ValorDeExtensionDePago</span>
            <span class="reference label label-danger" data-ref="@Saldo" id="lbl-saldo">@Saldo</span>
            <span class="reference label label-success" data-ref="@CodigoDeCliente" id="lbl-codigo">@CodigoDeCliente</span>
            <span class="reference label label-default" data-ref="@TelefonoFijo" id="lbl-telefono-fijo">@TelefonoFijo</span>
            <span class="reference label label-default" data-ref="@Celular1" id="lbl-celular1">@Celular1</span>
            <span class="reference label label-default" data-ref="@Celular2" id="lbl-celular2">@Celular2</span>
            <span class="reference label label-default" data-ref="@Celular3" id="lbl-celular3">@Celular3</span>
            <span class="reference label label-info" data-ref="@EmailPrincipal" id="lbl-email-principal">@EmailPrincipal</span>
            <span class="reference label label-info" data-ref="@EmailSecundario" id="lbl-email-secundario">@EmailSecundario</span>
        </div>
    </div>
    <hr/>
</div>

<script>
    var Email = new function() {
        this.init = function(){
            $(document).on('click', '.reference', function(){
                var new_value = CKEDITOR.instances.content.getData() + $(this).data('ref');
                CKEDITOR.instances['content'].setData(new_value);
                CKEDITOR.instances['content'].focus();
            });
        }
    }
</script>
<?php $this->registerJs('Email.init()') ?>
