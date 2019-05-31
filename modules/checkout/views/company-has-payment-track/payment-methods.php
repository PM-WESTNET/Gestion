<?php

use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\modules\checkout\models\PaymentMethod;
use app\modules\checkout\models\Track;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasPaymentTrack */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Config enabled payment method for this company and customers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company'), 'url' => ['company/view', 'id' => $model->company_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-create">

    <div class="row">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="customer-has-payment-track-form">

            <?php $form = ActiveForm::begin(); ?>

            <!-- Medios y canales de pago-->
            <div class="col-xs-12 well">
                <div class="form-group field-company-paymenttracks">
                    <div class="row col-sm-12">
                        <div class="col-sm-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'Payment methods') ?>
                            </label>
                        </div>
                        <div class="col-sm-3 text-center">
                            <label class="control-label">
                                <?= Yii::t('app', 'Enabled') ?>
                            </label>
                        </div>
                        <div class="col-sm-3 text-center">
                            <label class="control-label">
                                <?= Yii::t('app', 'Enabled for customers') ?>
                            </label>
                        </div>
                    </div>

                    <div>
                        <?php
                        $last_payment_method_id = 0;
                        foreach ($payment_methods as $payment_method) {
                            if($last_payment_method_id != $payment_method->payment_method_id) {
                                $payment_track_config = $model->getPaymentTracks()->where(['payment_method_id' => $payment_method->payment_method_id])->one();
                                $checked = $payment_track_config ? ($payment_track_config->payment_status == CompanyHasPaymentTrack::STATUS_ENABLED ?  'checked' : '')  : '';
                                $customer_checked = $payment_track_config ? ($payment_track_config->customer_status == CompanyHasPaymentTrack::STATUS_ENABLED ?  'checked' : '')  : ''; ?>

                                <div class="row col-sm-12">
                                    <div class="col-sm-6">
                                        <?= get_class($payment_method) == PaymentMethod::class ? $payment_method->name : $payment_method->paymentMethod->name?>
                                    </div>
                                    <div class="col-sm-3 text-center">
                                        <input class="availablility" data-pm-id="customer-availability-<?= $payment_method->payment_method_id ?>" type="checkbox" name="CompanyHasPaymentTrack[payment_status][<?= $payment_method->payment_method_id ?>]" id="availability-<?= $payment_method->payment_method_id ?>" <?= $checked ?> >
                                    </div>
                                    <div class="col-sm-3 text-center">
                                        <input class="customer-availability" data-pm-id="availability-<?= $payment_method->payment_method_id ?>" type="checkbox" name="CompanyHasPaymentTrack[customer_status][<?= $payment_method->payment_method_id ?>]" id="customer-availability-<?= $payment_method->payment_method_id ?>" <?= $customer_checked ?> >
                                    </div>
                                </div>

                        <?php }
                        $last_payment_method_id = $payment_method->payment_method_id;
                        } ?>
                    </div>
                </div>
            </div>
            <!-- Fin medios y canales de pago -->
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Next'), ['class' => 'btn btn-success pull-right']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<script>
    var PaymentMethods = new function() {

        this.init = function () {

            $('.availablility').on('click', function () {
                var id = $(this).attr('id');
                var customerId = $(this).data('pm-id');
                if($(this).is(':checked') == false){
                    document.getElementById(customerId).checked = false;
                    document.getElementById(customerId).disabled = true;
                } else {
                    document.getElementById(customerId).disabled = false;
                }
            });

            $.each($('.availablility'), function () {
                var id = $(this).attr('id');
                var customerId = $(this).data('pm-id');

                if($(this).is(':checked') == false){
                    document.getElementById(customerId).checked = false;
                    document.getElementById(customerId).disabled = true;
                } else {
                    document.getElementById(customerId).disabled = false;
                }
            })
        }
    }
</script>
<?php $this->registerJs('PaymentMethods.init()')?>

