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

$this->title = Yii::t('app', 'Customer Debts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>        
    </div>


    <div class="debtors-search">

    <?php
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

     Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_filters-debtors', ['searchModel' => $searchModel, 'action' => 'debtors']),
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
                '/sale/customer/export-debtors', 
                'CustomerSearch[customer_number]'=> $searchModel->customer_number,
                'CustomerSearch[name]' => $searchModel->name,
                'CustomerSearch[customer_status]' => $searchModel->customer_status,
                'CustomerSearch[toDate]' => $searchModel->toDate,
                'CustomerSearch[activatedFrom]' => $searchModel->activatedFrom,
                'CustomerSearch[debt_bills_from]' => $searchModel->debt_bills_from,
                'CustomerSearch[debt_bills_to]' => $searchModel->debt_bills_to,
                'CustomerSearch[contract_status]' => $searchModel->contract_status,
                'CustomerSearch[customer_class_id]' => $searchModel->customer_class_id,
                'CustomerSearch[amount_due]' => $searchModel->amount_due,
                'CustomerSearch[nodes]' => $searchModel->nodes,
                'CustomerSearch[company_id]' => $searchModel->company_id,
                'CustomerSearch[name]' => $searchModel->name,
            ]), ['class'=> 'btn btn-warning', 'target' => '_blank']
                
        )
    
    
    ?>

    

    <h2 class="text-danger"><?= Yii::t('app', 'Total Debt')?> : <?php $total= (double)Yii::$app->session->get('totalDebtors'); echo Yii::$app->formatter->asCurrency($total)?></h2>
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
            'attribute'=>'currency',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Amount due'),
        ],
        [
            'attribute'=> 'debt_bills',
            'label'=>Yii::t('app', 'Debt Bills'),
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
        'dataProvider' => $dataProvider,
        'id'=>'grid',
        'options' => ['class' => 'table-responsive'],                
        'columns' => $columns,
    ]); ?>

    <?php $grid->end(); ?>
</div>
