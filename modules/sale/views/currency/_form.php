<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Currency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'iso')->textInput(['maxlength' => 5]) ?>

    <?= $form->field($model, 'rate')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app','Enabled'), 'disabled' => Yii::t('app','Disabled'), ]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => 3]) ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
