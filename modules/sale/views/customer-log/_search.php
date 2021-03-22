<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\search\CustomerLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'customer_log_id') ?>

    <?= $form->field($model, 'action') ?>

    <?= $form->field($model, 'before_value') ?>

    <?= $form->field($model, 'new_value') ?>

    <?= $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'customer_customer_id') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'observations') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
