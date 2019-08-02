<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\PaymentMethod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-method-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app','Enabled'), 'disabled' => Yii::t('app','Disabled'), ]) ?>

    <?= $form->field($model, 'register_number')->checkbox() ?>

    <?= $form->field($model, 'send_ivr')->checkbox() ?>

    <?php
    $disabled = $model->isNewRecord ? [] : ['disabled'=>'disabled'];
    echo $form->field($model, 'type')->dropDownList([ 'exchanging' => Yii::t('app','Exchanging'), 'provisioning' => Yii::t('app','Provisioning'), 'account' => Yii::t('app','Account') ], $disabled) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
