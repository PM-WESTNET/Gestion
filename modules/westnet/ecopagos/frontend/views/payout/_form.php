<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Payout */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="payout-form margin-top-full">

    <?php
    $form = ActiveForm::begin([
        'id' => 'payout-form',
    ]);
    ?>

    <!-- Customer information -->
    <div class="row">
         <!-- Fetched customer information -->
        <div class="col-md-7" id="data-customer-information">

        </div>
        <!-- end Fetched customer information -->
        <div class="col-md-12">
            <div id="customer-info" class="panel panel-default position-relative z-depth-1 z-depth-important" style="z-index: 11;">

                <div class="panel-heading">
                    <h3 class="panel-title"><?= EcopagosModule::t('app', 'Information about this customer'); ?></h3>
                </div>

                <div class="panel-body">

                    <p id="barcode-instruction" class="text-muted">
                        <?= EcopagosModule::t('app', 'Make a scan over the bill number to get information about the customer, or enter it by hand and when finish, press enter.'); ?>
                    </p>
                    
                    <div class="label-big">
                        <?php if ($model->isNewRecord) : ?>

                            <?=
                            $form->field($model, 'customer_number')->textInput([
                                'maxlength' => 50,
                                'autocomplete' => 'off'
                            ])
                            ?>

                        <?php else: ?>

                            <?=
                            $form->field($model, 'customer_number')->textInput([
                                'maxlength' => 50,
                                'readonly' => 'readonly',
                                'autocomplete' => 'off'
                            ])
                            ?>

                        <?php endif; ?>                        
                    </div>
                    <h3>
                        <div id="customer-message">

                        </div>
                    </h3>
                </div>
            </div>
        </div>
       
    </div>
    <!-- end Customer information -->
    <div class="row">
        <div class="col-md-7 label-big">
            <?= $form->field($model, 'amount')->textInput(['autocomplete'=>'off']) ?>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label" >&nbsp;</label>
                <?= Html::button($model->isNewRecord ? EcopagosModule::t('app', 'Create Payout') : EcopagosModule::t('app', 'Update Payout'), [ 'id'=> 'btn-submit', 'class' => $model->isNewRecord ? 'btn btn-success form-control btn-height' : 'btn btn-primary form-control btn-height']) ?>
            </div>
        </div>
    </div>
    <!-- end Main ecopago information -->
    <?php ActiveForm::end(); ?>

</div>
<?= $this->registerJs("Payout.init();"); ?>
<?= $this->registerJs("Payout.setFetchCustomerInfoUrl('".yii\helpers\Url::to(['customer/get-customer-info'])."');"); ?>

<?php if($model->isNewRecord) : ?>

    <?= $this->registerJs("Payout.bindPermanentFocus();"); ?>

<?php endif; ?>
