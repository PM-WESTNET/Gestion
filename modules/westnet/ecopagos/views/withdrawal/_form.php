<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Withdrawal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withdrawal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'daily_closure_id')->textInput() ?>

    <?= $form->field($model, 'cashier_id')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'datetime')->textInput() ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
