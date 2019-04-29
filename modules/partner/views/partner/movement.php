<?php

use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */

$payment_method_bank = Config::getValue('payment_method_bank');;
$payment_method_paycheck = Config::getValue('payment_method_paycheck');
$payment_method_cash = Config::getValue('payment_method_cash');

$this->title = Yii::t('partner', 'Create '.($model->input ? 'Input': 'Withdraw' ).' of {name}', ['name'=>$model->getPartner()->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getPartner()->name, 'url' => ['view', 'id'=>$model->partner_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php echo app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?php
        echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]);
        echo $form->field($model, 'description')->textInput(['maxlength' => 150]);
    ?>

    <div class="form-group">
        <label><?= $model->getAttributeLabel('payment_method_id'); ?></label>
        <?php
        $methods = PaymentMethod::getPaymentMethods( false );
        ?>
        <select name="PartnerMovement[payment_method_id]" id="payment_method_id" class="form-control">
            <?php
            foreach ($methods as $method) {
                echo '<option value="'.$method->payment_method_id.'" '.($method->payment_method_id==$model->payment_method_id ? "selected" : "" ).' data-register-number="'.$method->register_number.'">'.$method->name.'</option>';
            }
            ?>
        </select>
        <?=$form->field($model, 'payment_method_id', ['template'=>'{error}']);?>
    </div>


    <?php
        echo $this->render('@app/modules/paycheck/views/paycheck/_paycheck-selector', ['model' => $model, 'for_payment' => true]);
        echo $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $model, 'id' => 'bank-account-selector', 'moneyBoxType' => Config::getValue('money_box_bank'), 'form' => $form, 'style' => 'horizontal']);
        echo $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $model, 'id' => 'smallbox-account-selector', 'moneyBoxType' => Config::getValue('money_box_smallbox'), 'form' => $form, 'style' => 'horizontal']);
        echo $form->field($model, 'amount')->textInput(['id'=>'amount']);
    ?>
    <div class="form-group">
        <?= Html::submitButton(  Yii::t('app', 'Create'), ['class' => 'btn btn-success' ]) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
    <script>
        var PartnerMovement = new function() {

            this.init = function() {
                $(document).on("change", "#payment_method_id", function(){
                    if ($(this).find("option:selected").data('register-number') == 1) {
                        $('#register-number').show(100);
                    }else{
                        $('#register-number').hide(100);
                    }

                    if ( $(this).val() == <?php echo $payment_method_bank ?>) {
                        $('#bank-account-selector').show(100);
                    }else{
                        $('#bank-account-selector').hide(100);
                    }

                    if ( $(this).val() == <?php echo $payment_method_cash ?>) {
                        $('#smallbox-account-selector').show(100);
                    }else{
                        $('#smallbox-account-selector').hide(100);
                    }

                    if ( $(this).val() == <?php echo $payment_method_paycheck ?>) {
                        $('#paycheck-selector').show(100);
                    }else{
                        $('#paycheck-selector').hide(100);
                    }
                });
                $("#payment_method_id").trigger("change");


                SearchPaycheck.onSelect = function (json){
                    $("#amount").val(json.paycheck.amount);
                }
            }

            this.save = function() {
                $("#provider-payment-form").submit();
            }

        }


    </script>
<?php $this->registerJs("PartnerMovement.init();"); ?>