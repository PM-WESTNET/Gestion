<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidationItem */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('westnet','Vendor Liquidation Item'),
]) . ' ' . $model->vendor_liquidation_item_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidation'). ' '.$model->vendorLiquidation->vendor->fullName, 'url' => ['vendor-liquidation/view', 'id' => $model->vendor_liquidation_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="vendor-liquidation-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'liquidation' => $liquidation
    ]) ?>

</div>
