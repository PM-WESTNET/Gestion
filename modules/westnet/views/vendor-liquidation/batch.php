<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */

$this->title = Yii::t('westnet', 'Batch Vendor Liquidation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendor Liquidations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_batch-form', [
        'model' => $model,
    ]) ?>

</div>
