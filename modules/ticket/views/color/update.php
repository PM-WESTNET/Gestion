<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Color */

$this->title = 'Update Color: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->color_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="color-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
