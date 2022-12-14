<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Vendor */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('westnet', 'Vendor'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet','Vendors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="user-vendor-create">

		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		        'address'=>$address,
                'user' => $user
		    ]) ?>

		</div>
	</div>
</div>
