<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\PeriodClosure */

$this->title = Yii::t('app', 'Create Period Closure');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Period Closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="period-closure-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
