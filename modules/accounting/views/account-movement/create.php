<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting', 'Manual Entry')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Account Movements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-movement-create">

    <div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
				'itemsDataProvider' => $itemsDataProvider
		    ]) ?>
    	</div>
    </div>

</div>
