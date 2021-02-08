<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Withdrawal */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Withdrawal',
]) . ' ' . $model->withdrawal_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Withdrawals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->withdrawal_id, 'url' => ['view', 'id' => $model->withdrawal_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="withdrawal-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
