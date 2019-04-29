<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\ProductCommission */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('westnet','Product Commission'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Commissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->product_commission_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-commission-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
