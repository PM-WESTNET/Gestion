<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\EventType */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Create {modelClass}', [
    'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Event type'),
]);
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Event types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="event-type-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
