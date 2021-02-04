<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Resume */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resume-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?= $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $model, 'form' => $form]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'date_from')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'date_to')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'balance_initial')->textInput() ?>

    <?= $form->field($model, 'balance_final')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Next') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
