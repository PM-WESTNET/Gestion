<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\TaxRate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tax-rate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pct')->textInput(['maxlength' => 5]) ?>
    
    <?= $form->field($model, 'tax_id')->dropDownList(yii\helpers\ArrayHelper::map(app\modules\sale\models\Tax::find()->all(), 'tax_id', 'name' )) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => 10]) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
