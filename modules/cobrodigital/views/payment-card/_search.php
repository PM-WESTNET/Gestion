<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\search\PaymentCardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-card-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'payment_card_id') ?>

    <?= $form->field($model, 'payment_card_file_id') ?>

    <?= $form->field($model, 'code_19_digits') ?>

    <?= $form->field($model, 'code_29_digits') ?>

    <?= $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'used') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
