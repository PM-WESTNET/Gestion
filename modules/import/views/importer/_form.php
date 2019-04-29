<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\ImportModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="import-form">

    <?php $form = ActiveForm::begin([
        'options'=>['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <?= $form->field($model, 'model')->dropDownList([ 'product' => Yii::t('app','Product') ]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> ' .Yii::t('import', 'Import'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
