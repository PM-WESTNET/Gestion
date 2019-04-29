<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\search\AddressSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'address_id') ?>

    <?= $form->field($model, 'street') ?>

    <?= $form->field($model, 'between_street_1') ?>

    <?= $form->field($model, 'between_street_2') ?>

    <?= $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'block') ?>

    <?php // echo $form->field($model, 'house') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'department') ?>

    <?php // echo $form->field($model, 'tower') ?>

    <?php // echo $form->field($model, 'zone_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
