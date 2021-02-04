<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\search\IntegratechSmsFilterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integratech-sms-filter-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'integratech_sms_filter_id') ?>

    <?= $form->field($model, 'word') ?>

    <?= $form->field($model, 'action') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
