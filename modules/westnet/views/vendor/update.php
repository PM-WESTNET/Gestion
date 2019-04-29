<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Vendor */

$this->title = Yii::t('app', 'Update {modelClass}', ['modelClass'=>Yii::t('westnet', 'Vendor')]) . ': ' . $model->name. ', ' . $model->lastname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet','Vendors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vendor_id, 'url' => ['view', 'id' => $model->vendor_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="user-vendor-update">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		        'user' => $user,
		        'address'=>$address,
		    ]) ?>

		</div>
	</div>
</div>
