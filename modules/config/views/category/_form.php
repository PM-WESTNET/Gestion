<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => ConfigModule::t('config','Enabled'), 'disabled' => ConfigModule::t('config','Disabled') ]) ?>

    <?= $form->field($model, 'superadmin')->checkbox() ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? ConfigModule::t('config', 'Create') : ConfigModule::t('config', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
