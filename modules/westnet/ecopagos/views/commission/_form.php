<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Commission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="commission-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'create_datetime')->textInput() ?>

    <?= $form->field($model, 'update_datetime')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'percentage' => 'Percentage', 'fixed' => 'Fixed', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'value')->textInput() ?>

    
    <?= $form->field($model, 'ecopago_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\westnet\ecopagos\models\Ecopago::find()->all(), 'ecopago_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
