<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\modules\westnet\components\ipStrategy\LegacyStrategy;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AccessPoint */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="access-point-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => 'Enabled', 'disabled' => 'Disabled', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'strategy_class')->hiddenInput(['value' => AccessPointStrategy::class ])->label(false) ?>

    <?= $form->field($model, 'node_id')->widget(Select2::class, [
            'data' => $nodes,
            'pluginOptions' => [
                'allowClear' => true
            ],
            'options' => ['placeholder' => Yii::t('app', 'Select an option')]
        ]) 
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
