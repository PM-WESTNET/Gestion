<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProductPrice $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="product-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'net_price')->textInput() ?>

    <?= $form->field($model, 'taxes')->textInput() ?>

    <?= $form->field($model, 'exp_date')->textInput() ?>

    <?= $form->field($model, 'exp_time')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <?= $form->field($model, 'timestamp')->textInput() ?>

    <?= $form->field($model, 'exp_timestamp')->textInput() ?>

    <?= $form->field($model, 'update_timestamp')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
