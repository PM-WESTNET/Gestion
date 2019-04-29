<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\search\ProviderPaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provider-payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'provider_payment_id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'provider_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
