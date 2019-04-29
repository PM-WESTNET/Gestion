<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_Id')->textInput() ?>

    <?= $form->field($model, 'task_id')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'read' => 'Read', 'unread' => 'Unread', ], ['prompt' => '']) ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
