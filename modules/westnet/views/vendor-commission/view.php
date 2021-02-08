<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorCommission */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Commissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-commission-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->vendor_commission_id], ['class' => 'btn btn-primary']) ?>
            <?php
            if ($model->deletable)
                echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->vendor_commission_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'vendor_commission_id',
            'name',
            'percentage',
            'value',
        ],
    ]) ?>
    
</div>
