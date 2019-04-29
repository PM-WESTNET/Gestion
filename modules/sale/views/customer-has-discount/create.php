<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */

$this->title = Yii::t('app', 'Apply discount to {customer}', ['customer' => $model->customer->name]);

$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id'=> $model->customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discounts applied'), 'url' => ['/sale/customer-has-discount/index', 'customer_id'=> $model->customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;

?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="customer-has-discount-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
