<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\checkout\models\PaymentMethod;


/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */

$this->title = Yii::t('app', 'Create manual debt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">

    <h1><?= Html::encode($this->title) ?> <small><?= $model->customer->fullName; ?></small></h1>
    
    <div class="payment-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'amount')->textInput() ?>

        <?= $form->field($model, 'concept')->textInput(['maxlength' => 255]) ?>

        <div class="form-group">
            <label><?= Yii::t('app','Account'); ?></label><br/>
            <span class="badge"><?= $model->paymentMethod->name; ?></span>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <script>

        var payment = new function(){

            this.init = function(){

                $('[data-register-number]').on('change',function(){
                    if($(this).attr('data-register-number') == 1){
                        $('#register-number').show(100);
                    }else{
                        $('#register-number').hide(100);
                    }
                })

            };
        }

    </script>

    <?php $this->registerJs('payment.init();'); ?>
    
</div>