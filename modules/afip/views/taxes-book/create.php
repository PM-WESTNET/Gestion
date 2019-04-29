<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('afip','Book ' . ucfirst($model->type))]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('afip','Book ' . ucfirst($model->type)), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="taxes-book-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
