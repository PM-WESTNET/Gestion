<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */

$this->title = Yii::t('app', 'Update {modelClass}: ', ['modelClass' => Yii::t('accounting', 'Conciliation'),]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Conciliations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->conciliation_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="conciliation-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
