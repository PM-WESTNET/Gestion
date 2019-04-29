<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Resume */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('accounting','Resume'),
]) . ' ' . $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Resumes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->resume_id, 'url' => ['view', 'id' => $model->resume_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="resume-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
