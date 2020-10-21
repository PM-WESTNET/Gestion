<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataCompanyConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-company-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->widget(Select2::class, [
        'data' => $companies,
        'pluginOptions' => [
            'allowClear' => $model->isNewRecord
        ],
        'options' => ['placeholder' => Yii::t('app', 'Select an company')]
    ]) ?>

    <?= $form->field($model, 'commerce_number')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
