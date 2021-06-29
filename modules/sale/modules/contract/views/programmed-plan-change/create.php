<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammedPlanChange */

$this->title = Yii::t('app', 'Create programmed plan change');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programmed plan changes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programmatic-change-plan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'customer' => $customer,
        'contract_id' => $contract_id,
        'plans' => $plans
    ]) ?>

</div>
