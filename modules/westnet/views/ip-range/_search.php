<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\search\IpRankSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ip-rank-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ip_rank_id') ?>

    <?= $form->field($model, 'ip_start') ?>

    <?= $form->field($model, 'ip_end') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'node_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
