<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-import-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'response_file')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
