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

$this->title = Yii::t('app', 'Enable payment tracks and set default');
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

                        <div id="company-paymenttracks">
                            <div class="row col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label">
                                        <?= Yii::t('app', 'Payment methods')?>
                                    </label>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <label class="control-label">
                                        <?= Yii::t('app', 'Enabled')?>
                                    </label>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <label class="control-label">
                                        <?= Yii::t('app', 'Default')?>
                                    </label>
                                </div>
                            </div>
                            <?php
                            $last_payment_method_id = 0;
                            foreach ($paymentTracks as $paymentTrack) {
                                $payment_track_config = $model->getPaymentTracks()->where(['payment_method_id' => $paymentTrack->payment_method_id, 'track_id' => $paymentTrack->track_id])->one();
                                $payment_checked = $payment_track_config->payment_track_status == CompanyHasPaymentTrack::STATUS_ENABLED ? 'checked'  : '';
                                $default_checked = $payment_track_config->default_track == CompanyHasPaymentTrack::STATUS_ENABLED ? 'checked' : ''; ?>

                                <?php if($last_payment_method_id != $paymentTrack->payment_method_id) { ?>
                                    <div class="row">
                                        <label class="control-label"> <?= $paymentTrack->paymentMethod->name ?> </label>
                                    </div>
                                <?php } ?>
                                <div class="row col-sm-12">
                                    <div class="col-sm-6">
                                        <?= $paymentTrack->track->name ?>
                                    </div>
                                    <div class="col-sm-3 text-center">
                                        <input class="available-checkbox" data-dc-id="<?= $paymentTrack->company_has_payment_track_id ?>" type="checkbox" name="CompanyHasPaymentTrack[<?= $paymentTrack->payment_method_id?>][payment_track_status][<?= $paymentTrack->track_id ?>]" <?= $payment_checked ?> >
                                    </div>
                                    <div class="col-sm-3 text-center">
                                        <input class="default-checkbox" id="<?= $paymentTrack->company_has_payment_track_id ?>" data-pm-id="<?= $paymentTrack->payment_method_id ?>" type="checkbox" name="CompanyHasPaymentTrack[<?= $paymentTrack->payment_method_id?>][default_track][<?= $paymentTrack->track_id ?>]" <?= $default_checked ?> >
                                    </div>
                                </div>

                            <?php
                            $last_payment_method_id = $paymentTrack->payment_method_id;
                            }?>
                        </div>
                    </div>
                </div>
                <!-- Fin medios y canales de pago -->
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Finalize'), ['class' => 'btn btn-success pull-right']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
    </div>
</div>

<script>
    var PaymentTrack = new function () {
        this.init = function () {
            //Verifico que no seleccione por defecto mas de un canal de pago
            $('.default-checkbox').on('click', function () {
                if($(this).is(':checked')) {
                    var pmId = $(this).data('pm-id');
                    var id = $(this).attr('id');
                    $.each($('.default-checkbox[data-pm-id="'+pmId+'"]'), function () {
                        if($(this).attr('id') !== id) {
                            document.getElementById($(this).attr('id')).checked = false;
                        }
                    })
                }
            })

            $('.available-checkbox').on('click', function () {
                var dcId = $(this).data('dc-id');
                if($(this).is(':checked') == false) {
                    document.getElementById(dcId).checked = false;
                    document.getElementById(dcId).disabled = true;
                } else {
                    document.getElementById(dcId).disabled = false;
                }
            })

            $.each($('.available-checkbox'), function () {
                var dcId = $(this).data('dc-id');
                if($(this).is(':checked') == false) {
                    document.getElementById(dcId).checked = false;
                    document.getElementById(dcId).disabled = true;
                } else {
                    document.getElementById(dcId).disabled = false;
                }
            })
        }
    }
</script>
<?php $this->registerJs('PaymentTrack.init()')?>


