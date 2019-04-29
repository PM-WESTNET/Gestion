<?php

use yii\helpers\Html;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Type */

$this->title = TicketModule::t('app', 'Update type: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->type_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="type-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
