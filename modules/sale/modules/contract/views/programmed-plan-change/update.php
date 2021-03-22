<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammedPlanChange */

$this->title = Yii::t('app', 'Update Programmed plan change: ' . $model->programmed_plan_change_id, [
    'nameAttribute' => '' . $model->programmed_plan_change_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programmed plan changes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->programmed_plan_change_id, 'url' => ['view', 'id' => $model->programmed_plan_change_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="programmatic-change-plan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
