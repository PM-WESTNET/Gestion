<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\search\PaymentCardFileSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-card-file-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'payment_card_file_id') ?>

    <?= $form->field($model, 'upload_date') ?>

    <?= $form->field($model, 'file_name') ?>

    <?= $form->field($model, 'path') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
