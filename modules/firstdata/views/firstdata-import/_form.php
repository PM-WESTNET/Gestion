<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-import-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'presentation_date')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'draft' => 'Draft', 'success' => 'Success', 'error' => 'Error', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'response_file')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'observation_file')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
