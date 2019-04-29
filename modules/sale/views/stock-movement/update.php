<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\StockMovement $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
  'modelClass' => Yii::t('app','Stock movement'),
]) . $model->stock_movement_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock movements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->stock_movement_id, 'url' => ['view', 'id' => $model->stock_movement_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="stock-movement-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
