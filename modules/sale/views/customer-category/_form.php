<?php

use app\modules\sale\models\Category;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\sale\models\CustomerCategory::find()->all(), 'customer_category_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app', 'Enabled'), 'disabled' => Yii::t('app', 'Disabled'), ], ['prompt' => '']) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
