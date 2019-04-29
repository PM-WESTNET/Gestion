<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\IpRank */

$this->title = 'Create Ip Rank';
$this->params['breadcrumbs'][] = ['label' => 'Ip Ranks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="ip-rank-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
