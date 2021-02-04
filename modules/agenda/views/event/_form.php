<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <?= $form->field($model, 'datetime')->textInput() ?>
    
    <?= $form->field($model, 'event_type_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\EventType::find()->all(), 'event_type_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
