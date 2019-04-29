<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Payout */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Payout',
]) . ' ' . $model->payout_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->payout_id, 'url' => ['view', 'id' => $model->payout_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="payout-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
