<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\jui\DatePicker;
use app\modules\westnet\models\PaymentExtensionHistory;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\search\PaymentExtensionHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-extension-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'from')->widget(Select2::class, [
                'data' => PaymentExtensionHistory::getFromTypesForSelect(),
                'pluginOptions' => [
                    'placeholder' => Yii::t('app', 'Select ...'),
                    'allowClear' => true
                ]
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'date_from')->widget(DatePicker::class, [
                'language' => Yii::$app->language,
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class'=>'form-control filter dates',
                    'placeholder'=>Yii::t('app','Date')
                ]
            ]);?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'date_to')->widget(DatePicker::class, [
                'language' => Yii::$app->language,
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class'=>'form-control filter dates',
                    'placeholder'=>Yii::t('app','Date')
                ]
            ]);?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
