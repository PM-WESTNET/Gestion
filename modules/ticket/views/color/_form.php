<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Color */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="color-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'color')->textInput(['maxlength' => 7]) ?>

    <?= $form->field($model, 'order')->textInput() ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => 45]) ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? TicketModule::t('app', 'Create') : TicketModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
