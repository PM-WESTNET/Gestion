<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Collector */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="collector-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 20]) ?>    

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'document_type')->dropDownList($model->fetchDocumentTypes(), ['prompt' => '']) ?>

    <?= $form->field($model, 'document_number')->textInput(['maxlength' => 20]) ?>
    
    <?= $form->field($model, 'limit')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
