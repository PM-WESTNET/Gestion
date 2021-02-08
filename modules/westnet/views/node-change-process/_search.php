<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\search\NodeChangeProcessSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="node-change-process-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'node_change_process_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'ended_at') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'node_id') ?>

    <?php // echo $form->field($model, 'creator_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
