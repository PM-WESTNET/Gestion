<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidationItem */
/* @var $liquidation app\modules\westnet\models\VendorLiquidation */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('westnet', 'Manual Liquidation Item')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidation'). ' '.$model->vendorLiquidation->vendor->fullName, 'url' => ['vendor-liquidation/view', 'id' => $model->vendor_liquidation_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'liquidation' => $liquidation
    ]) ?>

</div>
