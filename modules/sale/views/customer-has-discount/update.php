<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */

$this->title = Yii::t('app', 'Update') ." ".Yii::t('app', 'Discounts applied to {customer}', ['customer' => $model->customer->name]) . " - " . $model->discount->name;
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id'=> $model->customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discounts applied'), 'url' => ['/sale/customer-has-discount/index', 'customer_id'=> $model->customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;

$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="customer-has-discount-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
