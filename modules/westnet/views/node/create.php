<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Node */

$this->title =  Yii::t('app','Create {modelClass}', ['modelClass'=>Yii::t('westnet','Node')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet','Nodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="node-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
                'action' =>'create'
		       ]) ?>

		</div>
	</div>
</div>
