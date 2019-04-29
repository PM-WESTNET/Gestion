<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ContractDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contract-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'to_date')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'current' => 'Current', 'lapsed' => 'Lapsed', ], ['prompt' => '']) ?>

    
    <?= $form->field($model, 'product_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\sale\models\Product::find()->all(), 'product_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
