<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PointOfSale */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="point-of-sale-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= \app\components\companies\CompanySelector::widget(['model'=>$model, 'form'=>$form]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app','Enabled'), 'disabled' => Yii::t('app','Disabled') ]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => 255]) ?>
    
    <?= $form->field($model, 'default')->checkbox() ?>

    <?= $form->field($model, 'electronic_billing')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
