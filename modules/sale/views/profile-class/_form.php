<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProfileClass $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="profile-class-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'data_type')->dropDownList(['textInput'=>Yii::t('app','Short text'),'checkbox'=>Yii::t('app','Checkbox'),'textArea'=>Yii::t('app','Long text')]) ?>
    
    <?= $form->field($model, 'status')->dropDownList(['enabled'=>Yii::t('app','Enabled'),'disabled'=>Yii::t('app','Disabled')]) ?>

    <?= $form->field($model, 'hint')->textInput() ?>
    
    <?= $form->field($model, 'searchable')->checkbox()->hint(Yii::t('app','Too many searchable profiles could degrade the performance of Arya.')) ?>
    
    <?= $form->field($model, 'data_min')->textInput() ?>
    
    <?= $form->field($model, 'data_max')->textInput() ?>
    
    <?= $form->field($model, 'pattern')->textInput(['maxlength' => 45]) ?>
    
    <?= $form->field($model, 'order')->textInput(['maxlength' => 5]) ?>
    
    <?php // $form->field($model, 'multiple')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
