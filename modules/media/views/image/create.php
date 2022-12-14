<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\media\models\types\Image */

$this->title = Yii::t('app', 'Create Image');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Images'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="image-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
