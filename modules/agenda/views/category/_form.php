<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'default_duration')->widget(\kartik\time\TimePicker::classname(), [
        'pluginOptions' => [
            'defaultTime' => '02:00:00',
            'showMeridian' => false,
            'minuteStep' => 30
        ]
    ]); ?>

    <?php if ($model->isNewRecord) : ?>
        <?= $form->field($model, 'slug')->textInput() ?>    
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
