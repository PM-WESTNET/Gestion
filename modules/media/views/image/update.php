<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\media\models\types\Image */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Image',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->media_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="image-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
