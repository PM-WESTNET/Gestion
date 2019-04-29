<?php

use app\modules\sale\models\Discount;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-has-discount-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'customer_id')?>

    <?php
        if($model->isNewRecord) {
            echo $form->field($model, 'discount_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(\app\modules\sale\models\Discount::find()->all(), 'discount_id', 'name'),[
                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Discount')]),
                'encode'=>false,
                'separator'=>'<br/>',
            ])->label(Yii::t('app','Discount'));
        } else {
            echo Html::activeHiddenInput($model, 'discount_id');

        }
    ?>

    <?= $form->field($model, 'from_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?php
        if(!$model->isNewRecord) {
            echo $form->field($model, 'to_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR', 'dateFormat' => 'dd-MM-yyyy', 'options' => ['class' => 'form-control',],]);
        }
    ?>

    <?= $form->field($model, 'status')->dropDownList([ Discount::STATUS_ENABLED => Yii::t('app', 'Enabled'), Discount::STATUS_DISABLED => Yii::t('app', 'Disabled') ],
        ['prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app', 'Status')])]) ?>

    <?= $form->field($model, 'description')->textarea()->label(Yii::t('app', 'Description for Invoice')) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
