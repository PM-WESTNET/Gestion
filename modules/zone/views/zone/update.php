<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\zone\models\Zone */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app','Zone'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->zone_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="zone-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
