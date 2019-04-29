<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Checkbook */

$name = Yii::t('paycheck', 'Checkbook') . " " . $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;

$this->title = Yii::t('app', 'Update') . " - " . $name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('paycheck', 'Checkbooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $name, 'url' => ['view', 'id' => $model->checkbook_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="checkbook-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
