<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\search\NotifyPaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notify-payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-4">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model,'payment_method_id')->widget(Select2::class, [
                'data' => PaymentMethod::getPaymentMethodForSelect()
            ]);?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model,'from')->widget(Select2::class, [
                'data' => [
                    'App' => Yii::t('app','Mobile App'),
                    'IVR' => Yii::t('app','IVR'),
                ],
                'options' => ['placeholder' => Yii::t('app','Select an option')]
            ]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'from_date')->widget(DatePicker::class, [
                'language' => Yii::$app->language,
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class'=>'form-control filter dates',
                    'placeholder'=>Yii::t('app','Date')
                ]
            ]);?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'to_date')->widget(DatePicker::class, [
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
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
