<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Withdrawal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withdrawal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(EcopagosModule::t('app', 'Execute withdrawal'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
