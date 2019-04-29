<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PlanFeature */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => Yii::t('app','Plan Feature'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plan Features'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plan-feature-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
