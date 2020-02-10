<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeeBill */

$this->title = Yii::t('app', 'Create {modelClass}', [
	'modelClass' => Yii::t('app', 'Employee Payment'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-payment-create">

	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<h1>
				<?= Html::encode($this->title) ?>
				<small><?= ($model->employee ? $model->employee->name : "" )?></small>
			</h1>

			<?= $this->render('_form', [
				'model' => $model,
			]) ?>

	</div>

</div>
