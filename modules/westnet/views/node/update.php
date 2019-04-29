<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Node */

$this->title = Yii::t('app','Update', ['modelClass'=>Yii::t('westnet','Node')]) . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet','Nodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->node_id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="node-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
                'action' =>'update'
		        ]) ?>

		</div>
	</div>
</div>
