<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'before_value')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'new_value')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'observations')->textInput(['maxlength' => 300]) ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
