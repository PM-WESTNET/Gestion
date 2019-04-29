<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\pagomiscuentas\models\PagomiscuentasLiquidation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pagomiscuentas-liquidation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'account_movement_id')->textInput() ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
