
<?php

use app\modules\accounting\models\Account;
use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderBillHasProviderPayment;
use app\modules\sale\models\Tax;
use app\modules\sale\models\TaxRate;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */

$payment_method_bank = Config::getValue('payment_method_bank');;
$payment_method_paycheck = Config::getValue('payment_method_paycheck');
$payment_method_cash = Config::getValue('payment_method_cash');

    yii\widgets\Pjax::begin(['id' => 'new_item']);
    $form = ActiveForm::begin([
        'id'=>'provider-payment-item',
        'action' => ['add-item', 'id' => $model->provider_payment_id],
        'options' => ['data-pjax' => true, 'onsubmit'=>'return false;' ]]); ?>
    <input type="hidden" name="ProviderPaymentItem[provider_payment_id]" value="<?=$model->provider_payment_id?>"/>

    <div class="row">
        <div class="col-sm-9 col-md-3">
            <div class="form-group">
                <label><?= $item->getAttributeLabel('paymentMethod'); ?></label>
                <?php
                $methods = PaymentMethod::getPaymentMethods( !empty($model->customer_id)  );
                ?>
                <select name="ProviderPaymentItem[payment_method_id]" id="payment_method_id" class="form-control">
                    <?php
                    foreach ($methods as $method) {
                        echo '<option value="'.$method->payment_method_id.'" '.($method->payment_method_id==$item->payment_method_id ? "selected" : "" ).
                            ' data-register-number="'.$method->register_number.'">'.$method->name.'</option>';
                    }
                    ?>
                </select>
                <?=$form->field($item, 'payment_method_id', ['template'=>'{error}']);?>
            </div>

        </div>

        <?php if (Yii::$app->getModule('paycheck') ) { ?>
            <div class="col-sm-9 col-md-9">
                <?= $this->render('@app/modules/paycheck/views/paycheck/_paycheck-selector', ['model' => $item, 'for_payment' => true]); ?>
            </div>
        <?php } ?>

        <?php if (Yii::$app->getModule('accounting')  ) { ?>
            <div class="col-sm-9 col-md-9">
                <?= $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $item, 'id' => 'bank-account-selector', 'dropDownSuffix' => '_bank', 'moneyBoxType' => Config::getValue('money_box_bank'), 'form' => $form, 'style' => 'horizontal']); ?>
                <?= $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $item, 'id' => 'smallbox-account-selector', 'dropDownSuffix' => '_small', 'moneyBoxType' => Config::getValue('money_box_smallbox'), 'form' => $form, 'style' => 'horizontal']); ?>
            </div>
        <div class="col-sm-9 col-md-2"  style="display:none"  id="register-number">
                <?= $form->field($item, 'number')->textInput(['maxlength' => 45]) ?>
        </div>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-sm-9 col-md-6">
            <?= $form->field($item, 'description')->textInput()  ?>
        </div>
        <div class="col-sm-9 col-md-3">
            <?= $form->field($item, 'amount')->textInput(['id'=>'item_amount'])  ?>
        </div>
        <div class="col-sm-9 col-md-2">
            <label style="display: block">&nbsp;</label>
            <button class="btn btn-success" id="item-add" type="button">
                <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Add') ?>
            </button>
        </div>
    </div>

    <?php ActiveForm::end();
    yii\widgets\Pjax::end()
    ?>

<script>
    var ProviderPaymentItem = new function(){
        this.init = function() {
            $(document).off("change", "#payment_method_id")
                .on("change", "#payment_method_id", function(){
                    $('#register-number').val('');
                    $('#bank-account-selector').val('');
                    $('#smallbox-account-selector').val('');
                    $('#paycheck-selector').val('');
                    $('#money_box_id_bank').val('');
                    $('#money_box_id_small').val('');
                    $('#money_box_account_id_small').val('');
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

                $(document).off('keypress', "#item_amount")
                    .on('keypress', "#item_amount", function(event) {
                        event.stopPropagation();
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        var self = event.target;
                        if (keycode == 13) {
                            ProviderPayment.addItem();
                        }
                });

            });
            <?php if (Yii::$app->getModule('paycheck') ) { ?>
             SearchPaycheck.onSelect = function (json){
                 $("#item_amount").val(json.paycheck.amount);
             }
            <?php } ?>

            $("#payment_method_id").trigger("change");
        }
    }

</script>
<?php $this->registerJs('ProviderPaymentItem.init();'); ?>