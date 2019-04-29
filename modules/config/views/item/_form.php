<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'category_id')->dropDownList(yii\helpers\ArrayHelper::map(
        \app\modules\config\models\Category::find()->all(), 'category_id', 'name'
    )) ?>

    <?= $form->field($model, 'attr')->textInput(['maxlength' => 45]) ?>
    
    <?= $form->field($model, 'label')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'type')->dropDownList(app\modules\config\models\Item::types()) ?>

    <?= $form->field($model, 'default')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>
    
    <?= $form->field($model, 'superadmin')->checkbox() ?>

    <?= $form->field($model, 'multiple')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? ConfigModule::t('config', 'Create') : ConfigModule::t('config', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
