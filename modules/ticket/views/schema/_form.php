<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\modules\ticket\models\Schema;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Schema */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="schema-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'statuses')->widget(Select2::class, [
            'data' => Schema::getStatusesForSelect(),
            'options' => [
                    'multiple' => true
                ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
