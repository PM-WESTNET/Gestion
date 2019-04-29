<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBoxAccount */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('accounting','Money Box Account')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-box-account-create">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
