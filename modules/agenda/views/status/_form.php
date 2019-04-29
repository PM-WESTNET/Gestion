<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Status */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="status-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?php if($model->isNewRecord) : ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => 255]) ?>
    <?php endif; ?>    

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'color')->dropDownList([ 'danger' => 'Danger', 'success' => 'Success', 'warning' => 'Warning', 'info' => 'Info', 'primary' => 'Primary', 'normal' => 'Normal', ], ['prompt' => '']) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
