<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\ColorInput;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerClass */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-class-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'code_ext')->textInput() ?>

    <?= $form->field($model, 'is_invoiced')->checkbox() ?>

    <?= $form->field($model, 'tolerance_days')->textInput() ?>

    <?= $form->field($model, 'colour')->
            widget(ColorInput::classname(), [
                'options' => ['placeholder' => 'Select color ...'],
            ]);
    ?>

    <?= $form->field($model, 'percentage_bill')->textInput() ?>

    <?= $form->field($model, 'days_duration')->textInput() ?>

    <?= $form->field($model, 'percentage_tolerance_debt')->textInput() ?>

    <?= $form->field($model, 'service_enabled')->checkbox() ?>
    
    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app', 'Enabled'), 'disabled' => Yii::t('app', 'Disabled'), ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
