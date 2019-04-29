<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Transport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    
    <?= $form->field($model, 'class')->dropDownList($transportClasses) ?>

    <?= $form->field($model, 'status')->dropDownList(['enabled' => NotificationsModule::t('app','Enabled'), 'disabled' => NotificationsModule::t('app', 'Disabled')])?>

    <?php if ($model->isNewRecord) : ?>

        <?= $form->field($model, 'slug')->textInput(['maxlength' => 45]) ?>

    <?php endif; ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
