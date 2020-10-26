<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataExport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-export-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'firstdata_config_id')->widget(Select2::class, [
        'data' => $companies_config,
        'pluginOptions' => [
            'allowClear' => false
        ],
        'options' => ['placeholder' => Yii::t('app', 'Select an option...')]
    ]) ?>

    <?= $form->field($model, 'from_date')->widget(DatePicker::class, [
        'options' => ['class' => 'form-control'],
        'dateFormat' => 'dd-MM-yyyy'
    ])?>

    <?= $form->field($model, 'to_date')->widget(DatePicker::class, [
        'options' => ['class' => 'form-control'],
        'dateFormat' => 'dd-MM-yyyy'
    ])?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
