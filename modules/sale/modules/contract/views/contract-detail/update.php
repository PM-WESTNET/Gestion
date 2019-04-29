<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ContractDetail */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Contract Detail',
]) . ' ' . $model->contract_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->contract_id, 'url' => ['view', 'contract_id' => $model->contract_id, 'product_id' => $model->product_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="contract-detail-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
