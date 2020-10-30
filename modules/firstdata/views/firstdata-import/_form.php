<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataImport */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-import-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'money_box_account_id')->widget(Select2::class, [
        'data' => $accounts,
        'pluginOptions' => ['allowClear' => true],
        'options' => ['placeholder' => Yii::t('app', 'Select an Option...')]
    ])?>

    <?= $form->field($model, 'response')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
