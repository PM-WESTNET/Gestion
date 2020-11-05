<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataAutomaticDebit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-automatic-debit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->hasErrors()):?>
        <div class="alert alert-danger">
            <?= Html::errorSummary($model)?>
        </div>
    <?php endif;?>

    <div class="row">
        <div class="col-xs-12">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?= $form->field($model, 'status')->dropDownList([
                'enabled' => Yii::t('app', 'Enabled'),
                'disabled' => Yii::t('app', 'Disabled'),
            ])?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <label><?= Yii::t('app', 'Card Number')?></label>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <?= $form->field($model, 'block1')->textInput(['maxLength' => 4])->label(false)?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'block2')->textInput(['maxLength' => 4])->label(false)?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'block3')->textInput(['maxLength' => 4])->label(false)?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'block4')->textInput(['maxLength' => 4])->label(false)?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
