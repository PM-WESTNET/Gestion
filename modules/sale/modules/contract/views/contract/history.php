<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'History of Contract') .": " .  $model->contract_id ;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['/sale/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id'=> $model->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Number') .": " . $model->contract_id, 'url' => ['view', 'id' => $model->contract_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'History');
?>
<div class="contract-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <h4><?= Yii::t('app', 'Customer').": ".Html::encode($model->customer->name) ?></h4>

    <p>
        <?= Html::a(Yii::t('app', 'Back'), ['view', 'id' => $model->contract_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h2><?php echo Yii::t('app', 'Contract'); ?></h2>

        <?php
        echo GridView::widget([
        'dataProvider' => $dataContractLogs,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'from_date',
            'to_date',
            [
                'header' => Yii::t('app', 'Status') ,
                'value' => function($model) {
                    return Yii::t('app', ucfirst($model->status) );
                }
            ],
            [
                'header'=> Yii::t('app', 'Address') ,
                'value'=>function($model){
                    return $model->address->getFullAddress();
                }
            ],
        ],
    ]); ?>

    <?=GridView::widget([
        'dataProvider' => $dataContractDetailLogs,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'header'=>Yii::t('app', 'ID'),
                'attribute'=>'contract_detail_id',
                'group'=>true,
                'groupFooter'=>function ($model, $key, $index, $widget) {
                    return [
                        'mergeColumns'=>[[0,8]],
                        'options'=>['class'=>'success','style'=>'font-weight:bold;']
                    ];
                }
            ],
            [
                'header'=>Yii::t('app', 'Product Type'),
                'value' => function ($model) {
                    return Yii::t('app', ucfirst($model['type']));
                },
            ],
            [
                'header'=>Yii::t('app', 'Product'),
                'attribute'=>'product_name',
            ],
            [
                'header'=>Yii::t('app', 'Date'),
                'attribute'=>'date',
                'format'=>['date'],

            ],
            [
                'header'=>Yii::t('app', 'From Date'),
                'attribute'=>'from_date',
            ],
            [
                'header'=>Yii::t('app', 'To Date'),
                'attribute'=>'to_date',
            ],
            [
                'header'=>Yii::t('app', 'Status'),
                'value' => function ($model) {
                    return Yii::t('app', ucfirst($model['status'])) . ($model['contract_detail_log_id']==0 ?  '('. Yii::t('app', 'Current') . ')'  : '' );
                },
            ],
            [
                'header'=>Yii::t('app', 'Funding Plan'),
                'value' => function ($model) {
                    if(empty($model['qty_payments']) && empty($model['amount_payment']) ) {
                        return "";
                    } else {
                        return $model['qty_payments'] . " " . Yii::t('app', 'payments of') . " " .
                        Yii::$app->getFormatter()->asCurrency($model['amount_payment']);
                    }
                },
                'hAlign'=>'right',
            ],
            [
                'header'=>Yii::t('app', 'Total'),
                'attribute' => 'total',
                'format' => ['currency'],
                'hAlign'=>'right',
            ],
        ],
    ]); ?>

</div>