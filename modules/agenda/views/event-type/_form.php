<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\EventType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>    
    
    <?php if($model->isNewRecord) : ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => 45]) ?>
    <?php endif; ?>    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
