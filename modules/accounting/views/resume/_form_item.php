<?php

use yii\db\Expression;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Resume */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="resume-item-form">

    <?php $form = ActiveForm::begin([
        'id'=>'resume-item-add-form',
        'action' => ['add-item', 'resume_id' => $resume->resume_id],
        'enableClientValidation' => false,
        'options' => ['data-pjax' => true, 'onsubmit'=> 'return false' ]]);
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
                $input =  '<select name="ResumeItem[money_box_has_operation_type_id]" id="money_box_has_operation_type_id" class="form-control">';
                $input .= '<option value="">'.Yii::t('app', 'Select').'</option>';

                $operations = \app\modules\accounting\models\MoneyBoxHasOperationType::find()
                    ->where(['money_box_id'=> $resume->moneyBoxAccount->money_box_id])
                    ->andWhere(new Expression('money_box_account_id =' . $resume->money_box_account_id . ' or money_box_account_id is null'))->all();
                foreach ($operations as $operation) {
                    $input .= '<option value="' . $operation->money_box_has_operation_type_id . '" ' . ($operation->money_box_has_operation_type_id == $model->money_box_has_operation_type_id ? "selected" : "") .
                        ' data-is-debit="' . $operation->operationType->is_debit . '">' . $operation->operationType->name  . '</option>';
                }
            $input .= '</select>';
            echo $form->field($model, 'money_box_has_operation_type_id', ['template'=>'{label}'.$input.'{error}']);
            ?>
        </div>

        <div class="col-sm-9">
            <?= $form->field($model, 'description')->textInput(['maxlength' => 150]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'reference')->textInput(['maxlength' => 45]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'code')->textInput(['maxlength' => 45]) ?>
        </div>
        <div class="col-sm-4" style="display: none;" id="div_debit">
            <?= $form->field($model, 'debit')->textInput() ?>
        </div>
        <div class="col-sm-4" style="display: none;" id="div_credit">
            <?= $form->field($model, 'credit')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2 pull-left">
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' .Yii::t('app', 'Add'), "#", ['class' => 'btn btn-success btnAddResumeItem'] ) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>