<?php

use app\modules\sale\models\Discount;
use app\modules\sale\models\Product;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Discount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="discount-form">

    <?php $form = ActiveForm::begin()?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        Discount::STATUS_ENABLED => Yii::t('app', 'Enabled'),
        Discount::STATUS_DISABLED => Yii::t('app', 'Disabled')
    ],
        ['prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app', 'Status')])]) ?>

    <?= $form->field($model, 'type')->dropDownList([ Discount::TYPE_FIXED => Yii::t('app', 'Fixed'), Discount::TYPE_PERCENTAGE => Yii::t('app', 'Percentage'), ],
        ['prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app', 'Type')])]) ?>

    <?= $form->field($model, 'referenced')->checkbox() ?>

    <?= Html::label('Persistente indica que el descuento una vez generado al cliente, no tiene fecha de fin de vigencia, sino que se deshabilita despues de haberse aplicado la primera vez. Esto es para que en caso de no poder aplicarse, en la pŕoxima facturación se intente nuevamente', null, ['class' => 'hint-block']) ?>
    <?= $form->field($model, 'persistent')->checkbox() ?>

    <?= $form->field($model, 'value')->textInput() ?>

    <?= $form->field($model, 'from_date')->widget(DatePicker::class, ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control']]) ?>

    <?= $form->field($model, 'to_date')->widget(DatePicker::class, ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control']]) ?>

    <?= $form->field($model, 'periods')->textInput() ?>

    <?= $form->field($model, 'apply_to')->dropDownList([
            Discount::APPLY_TO_CUSTOMER => Yii::t('app', 'Customer'),
            Discount::APPLY_TO_PRODUCT => Yii::t('app', 'Product'),

        ], ['prompt' => Yii::t('app', 'Select')]) ?>

    <?= $form->field($model, 'value_from')->dropDownList([
        Discount::VALUE_FROM_TOTAL=> Yii::t('app', 'Total'),
        Discount::VALUE_FROM_PRODUCT => Yii::t('app', 'Product'),
        Discount::VALUE_FROM_PLAN => Yii::t('app', 'Plan'),
    ],['prompt' => Yii::t('app', 'Select')]) ?>

    <?= $form->field($model, 'product_id')->widget(Select2::className(),[
        'data' => ArrayHelper::map(Product::find()->all(), 'product_id', 'name'),
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
    var DiscountForm = new function() {
        this.init = function(){
            $(document).off('change', '#discount-value_from').on('change', '#discount-value_from', function(){
                DiscountForm.changeApply();
            });
            DiscountForm.changeApply();

        }

        this.changeApply = function(){
            if($('#discount-value_from').val() == '<?= Discount::VALUE_FROM_PRODUCT ?>') {
                $('.field-discount-product_id').show();
            } else {
                $('.field-discount-product_id').hide();
            }
        }
    }
</script>
<?php  $this->registerJs("DiscountForm.init();"); ?>