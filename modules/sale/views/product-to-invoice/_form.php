<?php

use app\modules\sale\models\Discount;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-has-discount-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'product_to_invoice_id')?>

    <div class="form-group">
        <label class="control-label"><?php echo Yii::t('app', 'Contract') ?></label>
        <label class="form-control"><?php echo $model->contractDetail->contract->description ?></label>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo Yii::t('app', 'Product') ?></label>
        <label class="form-control"><?php echo $model->contractDetail->product->name ?></label>
    </div>


    <div class="form-group">
        <label class="control-label"><?php echo Yii::t('app', 'Discount') ?></label>
        <label class="form-control"><?php echo ($model->discount?  $model->discount->name . " - " .
                Yii::t('app', $model->discount->type) . " - " . $model->discount->value
                : Yii::t('app', 'No apply') ); ?></label>
    </div>

    <?= $form->field($model, 'amount') ?>
    <?= $form->field($model, 'description') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
