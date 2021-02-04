<?php

use app\modules\automaticdebit\models\Bank;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\Bank */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        Bank::STATUS_ENABLED => Yii::t('app','Enabled'),
        Bank::STATUS_DISABLED => Yii::t('app','Disabled')
    ]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
