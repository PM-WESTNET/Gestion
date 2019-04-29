<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\TaxRate */

$this->title = Yii::t('app', 'Update'). ' ' . $model->tax_rate_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tax Rates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tax_rate_id, 'url' => ['view', 'id' => $model->tax_rate_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tax-rate-update">

     <div class="row">
    	<div class="col-sm-8 col-sm-offset-2">
		    <h1><?= Html::encode($this->title) ?></h1>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>
    	</div>
    </div>
    
</div>
