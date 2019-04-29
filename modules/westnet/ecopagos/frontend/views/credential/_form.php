<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Credential */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credential-form">

    <?php
    $form = ActiveForm::begin([
                'id' => 'payout-form'
    ]);
    ?>

    <!-- Customer information -->
    <?php if ($model->isNewRecord) : ?>
        <div id="customer-overlay" class="overlay"></div>
    <?php endif; ?>

    <div id="customer-info" class="panel panel-default position-relative z-depth-1 z-depth-important" style="z-index: 11;">

        <div class="panel-heading">
            <h3 class="panel-title"><?= EcopagosModule::t('app', 'Information about this customer'); ?></h3>
        </div>

        <div class="panel-body">

            <p class="text-muted">
                <?= EcopagosModule::t('app', 'Make a scan over the bill number to get information about the customer, or enter it by hand and when finish, press enter.'); ?>
            </p>

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

        <!-- Fetched customer information -->
        <div data-customer-information class="panel-footer">

        </div>
        <!-- end Fetched customer information -->

    </div>  
    <!-- end Customer information -->

    <div class="form-group">
        <?= Html::submitButton(EcopagosModule::t('app', 'Create Credential'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?= $this->registerJs("Payout.bindEnterPressEvent();"); ?>
<?= $this->registerJs("Payout.setFetchCustomerInfoUrl('" . yii\helpers\Url::to(['customer/get-customer-info']) . "');"); ?>

<?php if ($model->isNewRecord) : ?>

    <?= $this->registerJs("Payout.bindPermanentFocus();"); ?>

<?php endif; ?>