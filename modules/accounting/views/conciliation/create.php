<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Conciliation')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Conciliation'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="conciliation-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
