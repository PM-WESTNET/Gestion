<?php

use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app', 'Payment Plan')]);
$this->params['breadcrumbs'][] = ['label' => $customer->fullName, 'url' => ['/checkout/payment/current-account', 'customer'=>$customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">

    <div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?> <small><?= ($customer ? $customer->fullName : ""); ?></small></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
				'customer' => $customer,
				'payment' => $payment
		    ]) ?>
    	</div>
    </div>

</div>