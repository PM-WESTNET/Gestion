<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\helpers\UserA;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidationItem */

$this->title = Yii::t('westnet', 'Vendor Liquidation Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidation'), 'url' => ['vendor-liquidation/view', 'id' => $model->vendor_liquidation_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('westnet', 'Liquidation').' '.$model->vendorLiquidation->periodMonth, ['vendor-liquidation/view', 'id' => $model->vendor_liquidation_id], ['class' => 'btn btn-default']) ?>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->vendor_liquidation_item_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->vendor_liquidation_item_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'vendor_liquidation_item_id',
            [
                'attribute' => 'vendorLiquidation',
                'value' => Html::a($model->vendorLiquidation->periodMonth, ['vendor-liquidation/view', 'id' => $model->vendor_liquidation_id]),
                'label' => Yii::t('app', 'Period'),
                'format' => 'html'
            ],
            'amount:currency',
            'description',
            [
                'label' => Yii::t('app','Customer'),
                'value' => $model->contractDetail ? UserA::a($model->contractDetail->contract->customer->fullName,['/sale/customer/view', 'id' => $model->contractDetail->contract->customer->customer_id]) : NULL,
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app','Customer Number'),
                'value' => $model->contractDetail ? UserA::a($model->contractDetail->contract->customer->code,['/sale/customer/view', 'id' => $model->contractDetail->contract->customer->customer_id]) : NULL,
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app','Contract'),
                'value' => $model->contractDetail ? UserA::a($model->contractDetail->contract_id, ['/sale/contract/contract/view', 'id' => $model->contractDetail->contract_id]) : NULL,
                'format' => 'html'
            ],
            'bill_id',
        ],
    ]) ?>

</div>
