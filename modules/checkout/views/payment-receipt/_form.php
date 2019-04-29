<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-receipt-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <label><?= $model->getAttributeLabel('paymentMethod'); ?></label>
        <?php
        $methods = PaymentMethod::getPaymentMethods( !empty($model->customer_id) );
        foreach($methods as $method){
            echo '<div class="radio">';
            echo Html::activeRadio($model, 'payment_method_id', ['label'=>$method->name,'data-register-number'=>$method->register_number,'value'=>$method->payment_method_id,'uncheck'=>null,'id'=>'Payment_method_id'.$method->payment_method_id]);
            echo '</div>';
        }
        echo $form->field($model, 'payment_method_id', ['template'=>'{error}']);
        ?>
    </div>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'concept')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
