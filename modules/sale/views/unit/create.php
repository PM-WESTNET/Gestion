<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Unit $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => Yii::t('app','Unit'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unit-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
