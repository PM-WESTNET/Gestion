<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\search\AdsPercentagePerCompanySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ads-percentage-per-company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'percentage_per_company_id') ?>

    <?= $form->field($model, 'parent_company_id') ?>

    <?= $form->field($model, 'company_id') ?>

    <?= $form->field($model, 'percentage') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
