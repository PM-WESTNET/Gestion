<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\search\CustomerClassSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-class-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'customer_class_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'code_ext') ?>

    <?= $form->field($model, 'is_invoiced') ?>

    <?= $form->field($model, 'tolerance_days') ?>

    <?php // echo $form->field($model, 'colour') ?>

    <?php // echo $form->field($model, 'percentage_bill') ?>

    <?php // echo $form->field($model, 'days_duration') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
