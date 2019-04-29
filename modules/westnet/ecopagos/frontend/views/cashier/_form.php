<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Cashier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cashier-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .new-task-init{
        display: none;
    }
</style>