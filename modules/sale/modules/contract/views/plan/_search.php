<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\search\PlanSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="plan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'system') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'create_timestamp') ?>

    <?php // echo $form->field($model, 'update_timestamp') ?>

    <?php // echo $form->field($model, 'unit_id') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'uid') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
