<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\BatchClosure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="batch-closure-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'last_batch_closure_id')->textInput() ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <?= $form->field($model, 'datetime')->textInput() ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'total')->textInput() ?>

    <?= $form->field($model, 'payment_count')->textInput() ?>

    <?= $form->field($model, 'first_payout_number')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'last_payout_number')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'commision')->textInput() ?>

    <?= $form->field($model, 'discount')->textInput() ?>
    
    <?= $form->field($model, 'collector_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\westnet\ecopagos\models\Collector::find()->all(), 'collector_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>

    <?= $form->field($model, 'ecopago_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\westnet\ecopagos\models\Ecopago::find()->all(), 'ecopago_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
