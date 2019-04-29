<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Unit $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="unit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'type')->dropDownList(['int'=>Yii::t('app','Integer'),'float'=>Yii::t('app','Float')]) ?>

    <?= $form->field($model, 'symbol_position')->dropDownList(['prefix'=>Yii::t('app','Prefix'),'suffix'=>Yii::t('app','Suffix')]) ?>

    <?= $form->field($model, 'symbol')->textInput(['maxlength' => 10]) ?>
    
    <?= $form->field($model, 'code')->textInput(['maxlength' => 10]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
