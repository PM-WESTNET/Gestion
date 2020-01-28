<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeeBill */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app', 'Employee Bill'),
]) . ' ' . $model->employee->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->employee_bill_id, 'url' => ['view', 'id' => $model->employee_bill_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="employee-bill-update">

    <div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
                'dataProvider'=>$dataProvider,
                'itemsDataProvider' => $itemsDataProvider,
                'from' => $from
		    ]) ?>
    	</div>
    </div>
</div>
