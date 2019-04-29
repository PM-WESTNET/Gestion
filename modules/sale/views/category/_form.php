<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app','Enabled'), 'disabled' => Yii::t('app','Disabled'), ]) ?>

    <?php
    $conditions = null;
    if(!$model->isNewRecord){
        $conditions = ['<>','category_id',$model->category_id];
    }
    ?>
    
    <?= $form->field($model, 'parent_id')->dropDownList(yii\helpers\ArrayHelper::map(app\modules\sale\models\Category::find()->where($conditions)->all(), 'category_id', 'name'), ['prompt'=>Yii::t('app', 'Select parent category')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
