<?php

use yii\helpers\Html;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Category */

$this->title = TicketModule::t('app', 'Update category: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->category_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="category-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
