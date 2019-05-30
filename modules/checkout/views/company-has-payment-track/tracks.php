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

$this->title = Yii::t('app', 'Config enabled payment tracks');
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
                                    <?= Yii::t('app', 'Payment tracks')?>
                                </label>
                            </div>
                            <div class="col-sm-6 text-center">
                                <label class="control-label">
                                    <?= Yii::t('app', 'Enabled')?>
                                </label>
                            </div>
                        </div>

                        <div id="company-paymenttracks">
                            <?php foreach ($tracks as $track) {
                                $payment_track_config = $model->getPaymentTracks()->where(['track_id' => $track->track_id])->one();
                                $checked = $payment_track_config->track_status == CompanyHasPaymentTrack::STATUS_ENABLED ? 'checked' : '';
                                ?>

                                <div class="row col-sm-12">
                                    <div class="col-sm-6">
                                        <?= $track->name ?>
                                    </div>
                                    <div class="col-sm-6 text-center">
                                        <?php $checked = 'checked'; ?>
                                        <input type="checkbox" name="CompanyHasPaymentTrack[track_status][<?= $track->track_id?>]" <?= $checked ?> >
                                    </div>
                                </div>

                            <?php }?>
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


