<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Server */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('westnet','Server')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="server-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
