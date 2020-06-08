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

<?php
$this->registerJs("CKEDITOR.plugins.addExternal('xml', '".Yii::getAlias('@web')."/plugins/xml/plugin.js', '');");
$this->registerJs("CKEDITOR.plugins.addExternal('textwatcher', '".Yii::getAlias('@web')."/plugins/textwatcher/plugin.js', '');");
$this->registerJs("CKEDITOR.plugins.addExternal('ajax', '".Yii::getAlias('@web')."/plugins/ajax/plugin.js', '');");
$this->registerJs("CKEDITOR.plugins.addExternal('textmatch', '".Yii::getAlias('@web')."/plugins/textmatch/plugin.js', '');");
$this->registerJs("CKEDITOR.plugins.addExternal('autocomplete', '".Yii::getAlias('@web')."/plugins/autocomplete/plugin.js', '');");
$this->registerJs("CKEDITOR.plugins.addExternal('emoji', '".Yii::getAlias('@web')."/plugins/emoji/plugin.js', '');");
$this->registerJs("CKEDITOR.config.emoji_emojiListUrl = '".Yii::getAlias('@web')."/plugins/emoji/emoji.json'");
?>
<div class="mobile-push">

    <?= $form->field($model, 'subject')->widget(CKEditor::class, [
        'options' => ['id' => 'subject', 'rows' => 1],
        'preset' => 'basic',
        'clientOptions' => [
            'extraPlugins' => 'emoji',
        ]
    ]); ?>

    <?= $form->field($model, 'content')->widget(CKEditor::class, [
        'options' => ['rows' => 6, 'id' => 'content'],
        'preset' => 'basic',
        'clientOptions' => [
            'extraPlugins' => 'emoji',
            'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | emoji"
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

    <?= $form->field($model, 'buttom_payment_extension')->checkbox()?>
    <?= $form->field($model, 'buttom_payment_notify')->checkbox()?>
    <?= $form->field($model, 'buttom_edit_data')->checkbox()?>
    <?= $form->field($model, 'buttom_send_bill')->checkbox()?>
</div>

<script>
    var Email = new function() {
        this.init = function(){
            $(document).on('click', '.reference', function(){
                CKEDITOR.instances.content.insertText($(this).data('ref'))
                CKEDITOR.instances['content'].focus();
            });
        }
    }
</script>
<?php $this->registerJs('Email.init()') ?>
