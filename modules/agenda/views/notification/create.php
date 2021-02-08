<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Notification */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Create Notification');
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="notification-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
