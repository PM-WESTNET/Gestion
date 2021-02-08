<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\IpRank */

$this->title = 'Update Ip Rank: ' . ' ' . $model->ip_rank_id;
$this->params['breadcrumbs'][] = ['label' => 'Ip Ranks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ip_rank_id, 'url' => ['view', 'id' => $model->ip_rank_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="ip-rank-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
