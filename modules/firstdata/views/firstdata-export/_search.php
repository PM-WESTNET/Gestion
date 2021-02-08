<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\search\FirstdataExportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="firstdata-export-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'firstdata_export_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'file_url') ?>

    <?= $form->field($model, 'firstdata_config_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
