<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\FundingPlan */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Funding Plan',
]) . ' ' . $model->funding_plan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => $model->product->name, 'url' => ['product/view', 'id' => $model->product_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Funding Plans'), 'url' => ['index', 'id' => $model->product_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Funding Plan of').": ".$model->product->name, 'url' => ['view', 'id' => $model->funding_plan_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="funding-plan-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
