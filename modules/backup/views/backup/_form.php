<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backup\models\Backup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="backup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'init_timestamp')->textInput() ?>

    <?= $form->field($model, 'finish_timestamp')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'in_process' => 'In process', 'success' => 'Success', 'error' => 'Error', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
