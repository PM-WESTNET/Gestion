<?php

use app\modules\sale\models\search\CustomerSearch;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var CustomerSearch $searchModel
 */

$this->title = Yii::t('app', 'Positive Balance Customers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>        
    </div>


    <div class="debtors-search">

    <?php
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_filters-positive-balance', ['searchModel' => $search]),
                'encode' => false,
            ],
        ],
        'options' => [
            'class' => 'print',
            'aria-expanded' => 'false'
        ]
    ]);
    ?>
    </div>

    
   <?=      
        Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('app', 'Export'), Url::to(
            [
                '/sale/customer/export-positive-balance-customers', 
                'CustomerSearch[customer_number]'=> $search->customer_number,
                'CustomerSearch[name]' => $search->name,
                'CustomerSearch[customer_status]' => $search->customer_status,
                'CustomerSearch[toDate]' => $search->toDate,
                'CustomerSearch[activatedFrom]' => $search->activatedFrom,
                'CustomerSearch[debt_bills_from]' => $search->debt_bills_from,
                'CustomerSearch[debt_bills_to]' => $search->debt_bills_to,
                'CustomerSearch[contract_status]' => $search->contract_status,
                'CustomerSearch[customer_class_id]' => $search->customer_class_id,
                'CustomerSearch[amount_due]' => $search->amount_due,
                'CustomerSearch[nodes]' => $search->nodes,
                'CustomerSearch[company_id]' => $search->company_id,
                'CustomerSearch[name]' => $search->name,
            ]), ['class'=> 'btn btn-warning', 'target' => '_blank']
                
        )
    
    
    ?>

    <h2 class="text-success"><?= Yii::t('app', 'Total Positive Balance to Customers')?> : <?php $total= -(double)$totalBalance; echo Yii::$app->formatter->asCurrency($total)?></h2>

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
                'label' => Yii::t('app', 'Customer Number'),
                'value' => 'code',
        ],
        [
            'label' => Yii::t('app', 'Customer'),
            'attribute'=>'name',
        ],
        [
            'label' => Yii::t('app', 'Phone'),
            'attribute'=>'phone',
        ],
        [
            'attribute'=>'saldo',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Balance'),
            'value' => function ($model){
                return (-$model['saldo']);
            }
        ],
        
        [
            'class' => '\yii\grid\DataColumn',
            'content' => function($model, $key, $index, $column){
                return Html::a('<span class="glyphicon glyphicon-usd"></span> '.Yii::t('app','Account'), ['/checkout/payment/current-account','customer'=>$model['customer_id']], ['class'=>'btn btn-width btn-default']);
            },
            'format'=>'html',
        ]
    ];

    $grid = GridView::begin([
        'dataProvider' => $provider,
        'id'=>'grid',
        'options' => ['class' => 'table-responsive'],                
        'columns' => $columns,
    ]); ?>

    <?php $grid->end(); ?>
</div>
