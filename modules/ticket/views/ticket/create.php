<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Ticket */

$this->title = \app\modules\ticket\TicketModule::t('app', 'Create Ticket');
$this->params['breadcrumbs'][] = ['label' => \app\modules\ticket\TicketModule::t('app', 'Tickets'), 'url' => ['open-tickets']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="ticket-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
