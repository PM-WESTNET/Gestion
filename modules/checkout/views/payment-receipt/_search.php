<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\PaymentReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-receipt-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'payment_receipt_id') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'time') ?>

    <?= $form->field($model, 'concept') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'datetime') ?>

    <?php // echo $form->field($model, 'customer_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
