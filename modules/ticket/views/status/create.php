<?php

use yii\helpers\Html;
use app\modules\ticket\TicketModule;


/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Status */

$this->title = TicketModule::t('app', 'Create Status');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="status-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
                'status_has_action' => $status_has_action
            ]) ?>

		</div>
	</div>
</div>
