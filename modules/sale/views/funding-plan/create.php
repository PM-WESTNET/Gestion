<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\FundingPlan */

$this->title = Yii::t('app', 'Funding Plan of').": ".$product->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['view', 'id' => $product->product_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Funding Plans'), 'url' => ['index', 'id' => $product->product_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="funding-plan-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		        'product' => $product,
		    ]) ?>

		</div>
	</div>
</div>
