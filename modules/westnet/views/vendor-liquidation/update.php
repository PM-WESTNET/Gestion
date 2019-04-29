<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('westnet','Vendor Liquidation'),
]) . ' ' . $model->vendor_liquidation_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendor Liquidations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vendor_liquidation_id, 'url' => ['view', 'id' => $model->vendor_liquidation_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="vendor-liquidation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
