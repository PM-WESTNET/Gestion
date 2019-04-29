<?php

use app\modules\accounting\models\AccountingPeriod;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountingPeriod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="accounting-period-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'date_from')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'date_to')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'status')->dropDownList( [AccountingPeriod::STATE_OPEN => Yii::t('accounting', ucfirst(AccountingPeriod::STATE_OPEN)), AccountingPeriod::STATE_CLOSED=> Yii::t('accounting', ucfirst(AccountingPeriod::STATE_CLOSED))], [
        'prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app', 'Status')]).'...'] )  ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
