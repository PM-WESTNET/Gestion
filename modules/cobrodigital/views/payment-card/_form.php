<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-card-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'payment_card_file_id')->textInput() ?>

    <?= $form->field($model, 'code_19_digits')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code_29_digits')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'used')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
