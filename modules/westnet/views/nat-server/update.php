<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NatServer */

$this->title = 'Actualizar Nat Server: ' . ' ' . $model->nat_server_id;
$this->params['breadcrumbs'][] = ['label' => 'Nat Server', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nat_server_id, 'url' => ['view', 'id' => $model->nat_server_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="nat-server-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
