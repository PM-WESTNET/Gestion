<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\Provider */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Provider',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Providers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->provider_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="provider-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
