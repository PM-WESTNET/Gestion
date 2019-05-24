<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\PaymentMethod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-method-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app','Enabled'), 'disabled' => Yii::t('app','Disabled'), ]) ?>

    <?= $form->field($model, 'register_number')->checkbox() ?>

    <?= $form->field($model, 'allow_track_config')->checkbox() ?>

    <?= $form->field($model, 'type_code_if_isnt_direct_channel')->widget(Select2::class, [
            'data' => PaymentMethod::getTypeCodesForSelect(),
            'pluginOptions' => [
                'allowClear' => true
            ],
            'options' => [
                'placeholder' => Yii::t('app', 'Select ...')
            ]
    ]) ?>

    <?php
    $disabled = $model->isNewRecord ? [] : ['disabled'=>'disabled'];
    echo $form->field($model, 'type')->dropDownList([ 'exchanging' => Yii::t('app','Exchanging'), 'provisioning' => Yii::t('app','Provisioning'), 'account' => Yii::t('app','Account') ], $disabled) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
