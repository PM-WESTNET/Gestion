<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */

$this->title = Yii::t('app', 'Product to Invoice') . " - " . $model->contractDetail->product->name;
$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/sale/customer/view', 'id'=> $customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products to Invoice'), 'url' => ['/sale/contract/product-to-invoice/index', 'customer_id'=> $customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;


$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="customer-has-discount-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		        'customer' => $customer,
		    ]) ?>

		</div>
	</div>
</div>
