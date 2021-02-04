<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Event */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Event',
]) . ' ' . $model->event_id;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Events'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->event_id, 'url' => ['view', 'id' => $model->event_id]];
$this->params['breadcrumbs'][] = \app\modules\agenda\AgendaModule::t('app', 'Update');
?>

<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="event-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
