<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('accounting','Entry'),
]) . ' ' . $model->account_movement_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Diary Book'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->account_movement_id, 'url' => ['view', 'id' => $model->account_movement_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="account-movement-update">

    <div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
				'itemsDataProvider' => $itemsDataProvider
		    ]) ?>
    	</div>
    </div>

</div>
