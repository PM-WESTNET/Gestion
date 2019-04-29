<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Type */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app','Ticket Type')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="type-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
