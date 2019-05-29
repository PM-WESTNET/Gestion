<?php
use dosamigos\ckeditor\CKEditor;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'subject')->textInput(['maxlength' => 255]) ?>
<?=
$form->field($model, 'content')->widget(CKEditor::class, [
    'options' => ['rows' => 6, 'id' => 'content'],
    'clientOptions' => [
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    ]
]);
?>

<div class="row">
    <div class="col-sm-3">
        <?= Yii::t('app', 'References') ?>:
    </div>
    <div class="col-sm-9">
        <span class="reference label label-default" data-ref="@Nombre" id="lbl-nombre">@Nombre</span>
        <span class="reference label label-primary" data-ref="@Telefono1" id="lbl-telefono1">@Telefono1</span>
        <span class="reference label label-success" data-ref="@Telefono2" id="lbl-telefono2">@Telefono2</span>
        <span class="reference label label-info" data-ref="@Code" id="lbl-codigo"></span>
        <span class="reference label label-warning" data-ref="@PaymentCode" id="lbl-codigo-de-pago">@CodigoDePago</span>
        <span class="reference label label-danger" data-ref="@CompanyCode" id="lbl-codigo-empresa">@CodigoEmpresa</span>
        <span class="reference label label-default" data-ref="@FacturasAdeudadas" id="lbl-facturas-adeudadas">@FacturasAdeudadas</span>
        <span class="reference label label-primary" data-ref="@Saldo" id="lbl-saldo">@Saldo</span>
        <span class="reference label label-success" data-ref="@Estado"></span>
        <span class="reference label label-info" data-ref="@Categoria" id="lbl-categoria">@Categoria</span>
    </div>
</div>
<hr/>

<?= $form->field($model, 'layout')->dropDownList(LayoutHelper::getLayouts()); ?>

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