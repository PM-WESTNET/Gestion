<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountingPeriod */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('accounting', 'Accounting Period'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Accounting Periods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->accounting_period_id]];
$this->params['breadcrumbs'][] = Yii::t('accounting', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="accounting-period-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
