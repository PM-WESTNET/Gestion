<?php

use app\modules\checkout\models\PaymentMethod;
use app\modules\checkout\models\Track;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

?>
<!-- Medios y canales de pago-->
<div class="col-xs-12 well hidden" id="customer-payment-tracks">
    <div class="form-group field-company-paymenttracks">
        <label class="control-label">
            <?= Yii::t('app', 'Payment methods and tracks')?>
        </label>

        <div>
            <?php foreach ($paymentMethods as $payment_method) {
                $payment_track_config = $model->getPaymentTracks()->where(['payment_method_id' => $payment_method->payment_method_id])->one(); ?>

                <div class="row col-sm-12">
                    <div class="col-sm-6">
                        <label>
                             <?= $payment_method->name ?>
                        </label>
                    </div>

                    <div class="col-sm-6">
                        <?= Select2::widget([
                            'data' => ArrayHelper::map(Track::find()->all(), 'track_id', 'name'),
                            'name' => "Customer[paymentTracks][Track][$payment_method->payment_method_id]",
                            'value' => $payment_track_config ? $payment_track_config->track_id : ''
                        ])?>
                        <br>
                    </div>
                </div>

            <?php }?>
        </div>
    </div>
</div>
<!-- Fin medios y canales de pago -->
