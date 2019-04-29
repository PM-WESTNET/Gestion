<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\sale\models\InvoiceClass;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\BillType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bill-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'code')->textInput() ?>
    
    <?= $form->field($model, 'customer_required')->checkbox() ?>
    
    <?= $form->field($model, 'startable')->checkbox() ?>

    <?= $form->field($model, 'billTypes')->checkboxList( ArrayHelper::map(app\modules\sale\models\BillType::find()->all(), 'bill_type_id', 'name'), ['separator' => '<br>'] ) ?>
    
    <?= $form->field($model, 'view')->dropDownList(['default' => Yii::t('app','Default'), 'final' => Yii::t('app', 'Final'), 'delivery-note' => Yii::t('app', 'Delivery note')]) ?>
    
    <?= $form->field($model, 'multiplier')->dropDownList([1=>'1', 0 => '0',-1=>'-1']) ?>
    
    <?= $form->field($model, 'class')->dropDownList( app\modules\sale\components\BillExpert::getBillClasses(true), ['prompt' => Yii::t('app', 'Select')] ) ?>
    
    <?= $form->field($model, 'invoice_class_id')->dropDownList( ArrayHelper::map(InvoiceClass::find()->all(), 'invoice_class_id', 'name'), ['prompt' => Yii::t('app', 'Select')] ) ?>

    <?= $form->field($model, 'applies_to_buy_book')->checkbox() ?>

    <?= $form->field($model, 'applies_to_sale_book')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? ' btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
