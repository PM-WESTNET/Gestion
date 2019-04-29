<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBoxAccount */

$this->title = Yii::t('app', 'Update') . ' ' . $model->moneyBox->name . " - " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->moneyBox->name . " - " . $model->number, 'url' => ['view', 'id' => $model->money_box_account_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="money-box-account-update">

    <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
