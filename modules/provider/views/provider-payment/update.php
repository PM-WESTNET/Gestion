<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\ProviderPayment */
$this->title = Yii::t('app', 'Update') . " " .  Yii::t('app', 'Payment to') . $model->provider->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Provider Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->provider_payment_id, 'url' => ['view', 'id' => $model->provider_payment_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="container">
<div class="provider-payment-update">

    <div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'billDataProvider' => $billDataProvider
		    ]) ?>
    	</div>
    </div>
</div>
</div>