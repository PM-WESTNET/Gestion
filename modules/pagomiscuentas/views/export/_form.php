<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <input name="PagomiscuentasFile[type]" value="bill" type="hidden"/>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <div class="form-group">
        <?php
        echo $form->field($model, 'from_date')->widget(yii\jui\DatePicker::className(), [
            'language' => Yii::$app->language,
            'model' => $model,
            'attribute' => 'from_date',
            'dateFormat' => 'dd-MM-yyyy',
            'options'=>[
                'class'=>'form-control dates',
                'id' => 'from-date'
            ]
        ])->label(Yii::t('pagomiscuentas', 'Bills From Date'));
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->field($model, 'date')->widget(yii\jui\DatePicker::className(), [
            'language' => Yii::$app->language,
            'model' => $model,
            'attribute' => 'date',
            'dateFormat' => 'dd-MM-yyyy',
            'options'=>[
                'class'=>'form-control dates',
                'id' => 'to-date'
            ]
        ])->label(Yii::t('pagomiscuentas', 'Bills To Date'));
        ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>