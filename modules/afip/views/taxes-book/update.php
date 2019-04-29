<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */

$this->title = Yii::t('afip', 'Update {modelClass}: ', [
    'modelClass' => 'Taxes Book',
]) . ' ' . $model->taxes_book_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('afip', 'Taxes Books'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->taxes_book_id, 'url' => ['view', 'id' => $model->taxes_book_id]];
$this->params['breadcrumbs'][] = Yii::t('afip', 'Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="taxes-book-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
