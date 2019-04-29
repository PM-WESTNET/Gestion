<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\StockMovement $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => Yii::t('app','Stock movement'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock movements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-movement-create">
	<!-- TO DO: ver si esta bien esta vista -->
    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <h4><?= Yii::t('app','Product') ?>: <?= $model->product->name; ?> #<?= $model->product->code; ?></h4>
		    <hr/>
		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
		    
		    <hr/>
		    <h4 class="pull-right"><?= Yii::t('app','Product') ?>: <?= $model->product->name; ?> #<?= $model->product->code; ?></h4>
    	</div>
    </div>

</div>
