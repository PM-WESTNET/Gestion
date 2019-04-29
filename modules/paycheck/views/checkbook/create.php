<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Checkbook */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('paycheck','Checkbook')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('paycheck', 'Checkbooks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="checkbook-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
