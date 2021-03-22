<?php

use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conciliation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <div class="form-group" id="bank-account-selector">
        <?= $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $model, 'form' => $form, 'id'=>'money_box_account_id', 'dropDownSuffix'=>'resume']); ?>
    </div>

    <?php
        if ($model->resume!==null) {
            $data = [$model->resume_id=>$model->resume->name];
        } else {
            $data = [];
        }
        echo $form->field($model, 'resume_id')->widget(DepDrop::classname(), [
            'options' => ['id' => 'resume_id'],
            'data' => $data,
            'pluginOptions' => [
                'depends' => ['money_box_account_idresume'],
                'initDepends' => 'money_box_account_idresume',
                'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('accounting','Resume')]),
                'url' => Url::to(['/accounting/resume/resume-by-account'])
            ]
        ])->label(Yii::t('accounting', 'Resume'));
    ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'date_from')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'date_to')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Next') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
