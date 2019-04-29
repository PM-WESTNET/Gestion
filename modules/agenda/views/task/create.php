<?php

use yii\helpers\Html;
use \app\modules\agenda\AgendaModule;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Task */

$this->title = AgendaModule::t('app', 'Create Task');
$this->params['breadcrumbs'][] = ['label' => AgendaModule::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="task-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
