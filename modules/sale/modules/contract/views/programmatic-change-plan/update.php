<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammaticChangePlan */

$this->title = Yii::t('app', 'Update Programmatic Change Plan: ' . $model->programmatic_change_plan_id, [
    'nameAttribute' => '' . $model->programmatic_change_plan_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programmatic Change Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->programmatic_change_plan_id, 'url' => ['view', 'id' => $model->programmatic_change_plan_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="programmatic-change-plan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
