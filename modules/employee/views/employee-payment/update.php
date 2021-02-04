<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeePayment */
$this->title = Yii::t('app', 'Update') . " " .  Yii::t('app', 'Payment to') . $model->employee->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->employee_payment_id, 'url' => ['view', 'id' => $model->employee_payment_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="container">
<div class="employee-payment-update">

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