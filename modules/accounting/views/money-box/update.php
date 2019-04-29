<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBox */

$this->title = Yii::t('app', 'Update') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Boxes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->money_box_id]];
$this->params['breadcrumbs'][] = Yii::t('accounting', 'Update');
?>
<div class="money-box-update">

    <div class="row">
    	<div class="col-sm-12">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>

</div>
