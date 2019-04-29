<?php

use yii\helpers\Html;
use app\modules\westnet\ecopagos\EcopagosModule;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Cashier */

$this->title = EcopagosModule::t('app', 'Create Cashier');
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Cashiers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="cashier-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>
	</div>
</div>
