<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\BankCompanyConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-company-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= \app\components\companies\CompanySelector::widget(['model' => $model, 'form' => $form]) ?>

    <?= $form->field($model, 'account_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'service_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_service_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_identification')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_company_identification')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'control_digit')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
