<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\EventType */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Update {modelClass}: ', [
    'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Event type'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Event types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->event_type_id]];
$this->params['breadcrumbs'][] = \app\modules\agenda\AgendaModule::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="event-type-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
