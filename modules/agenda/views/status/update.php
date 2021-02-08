<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Status */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Status',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Task Statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->status_id]];
$this->params['breadcrumbs'][] = \app\modules\agenda\AgendaModule::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="status-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
