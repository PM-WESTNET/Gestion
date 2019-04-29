<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProductPrice $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
  'modelClass' => Yii::t('app','Product price'),
]) . $model->product_price_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_price_id, 'url' => ['view', 'id' => $model->product_price_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-price-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
