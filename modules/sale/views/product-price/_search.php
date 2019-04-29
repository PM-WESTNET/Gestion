<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\search\ProductPriceSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="product-price-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'product_price_id') ?>

    <?= $form->field($model, 'net_price') ?>

    <?= $form->field($model, 'taxes') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'time') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <?php // echo $form->field($model, 'exp_timestamp') ?>

    <?php // echo $form->field($model, 'exp_date') ?>

    <?php // echo $form->field($model, 'exp_time') ?>

    <?php // echo $form->field($model, 'update_timestamp') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'product_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
