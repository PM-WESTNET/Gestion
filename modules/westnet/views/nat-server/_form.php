<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NatServer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nat-server-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 100]) ?>

    
    <?= $form->field($model, 'status')->dropdownList([
        1 => Yii::t('app', 'Enabled'),
        0 => Yii::t('app', 'Disabled'),
    ]) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
