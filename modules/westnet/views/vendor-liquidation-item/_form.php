<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidationItem */
/* @var $form yii\widgets\ActiveForm */
/* @var $liquidation app\modules\westnet\models\VendorLiquidation */
?>

<div class="vendor-liquidation-item-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <h3><?= $liquidation->vendor->fullName ?></h3>
    <h4><?= Yii::t('app', 'Period') .': '. $liquidation->periodMonth ?></h4>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>
    
    <?= $form->field($model, 'contract_detail_id')->widget(Select2::className(),[
        'data' => app\modules\sale\modules\contract\models\ContractDetail::getForLiquidationSelect(),
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);
    ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
