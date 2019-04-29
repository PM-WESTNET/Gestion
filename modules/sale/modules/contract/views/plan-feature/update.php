<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PlanFeature */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Plan Feature',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plan Features'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->plan_feature_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="plan-feature-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
