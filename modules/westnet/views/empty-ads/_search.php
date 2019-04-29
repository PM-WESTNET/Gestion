<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\search\EmptyAdsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="empty-ads-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'empty_ads_id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'payment_code') ?>

    <?= $form->field($model, 'node_id') ?>

    <?= $form->field($model, 'used') ?>

    <?php // echo $form->field($model, 'company_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
