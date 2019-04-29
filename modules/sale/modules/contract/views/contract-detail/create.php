<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ContractDetail */

$this->title = Yii::t('app', 'Create Contract Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="contract-detail-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
