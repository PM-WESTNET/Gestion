<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\search\ProviderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provider-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'provider_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'business_name') ?>

    <?= $form->field($model, 'tax_identification') ?>

    <?= $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'bill_type') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'phone2') ?>

    <?php // echo $form->field($model, 'description') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
