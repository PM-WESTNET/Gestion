<?php

use app\modules\sale\models\Address;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use kartik\export\ExportMenu;
use yii\bootstrap\Collapse;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;


/**
 * @var View $this
 * @var ContractSearch $contract_search
 */

$this->title = Yii::t('app', 'Installations');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="installations">
    <h1> <?= $this->title?></h1>
    
    <div class="installations-search">

    <?php
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

        echo Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_filters-installations', ['model' => $contract_search]),
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
    ExportMenu::widget([
        'dataProvider' => $data,
        'columns' => [
            [
                'label'=> Yii::t('app', 'Customer Number'),
                'value'=>function($model){
                    return  $model['code'];
                },
                'contentOptions' => ['style' => 'text-align: left'],
            ],
            [
                'label'=> Yii::t('app', 'Customer'),
                'value'=>function($model){
                    return  $model['name'];
                },
                'contentOptions' => ['style' => 'text-align: left'],
            ],
            [
                'label'=> Yii::t('app', 'Phones'),
                'value'=>function($model){
                    return  $model['phones'];
                }
            ],
            [
                'label'=> Yii::t('app', 'Email'),
                'value'=>function($model){
                    return  $model['email'];
                }
            ]
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $data,
        'columns' => [
            [
                'label' => Yii::t('app', 'Customer Number'),
                'attribute' => 'code'
            ],
            [
                'label' => Yii::t('app', 'Customer'),
                'attribute' => 'name'
            ],
            [
                'label' => Yii::t('westnet', 'Connection'),
                'value' => function($model){
                    $a = Address::findOne(['address_id' => $model['address_id']]);
                    if($a){
                        return $a->fullAddress;
                    }
                    return "";
                }
            ],
            [
                'label' => Yii::t('app', 'Phones'),
                'attribute' => 'phones'
            ],
            [
                'label' => Yii::t('app', 'From Date'),
                'value' => function($model){
                    return Yii::$app->formatter->asDate($model['from_date'], 'dd-MM-yyyy');
                },
            ],        
            [
                'label' => Yii::t('app', 'Bills Count'),
                'attribute' => 'bills',
            ],
            
            [
                'label' => Yii::t('app', 'Balance'),
                'value' => function ($model){
                    return Yii::$app->formatter->asCurrency($model['saldo']);
                }
            ],
            [
                'label' => Yii::t('app', 'Tickets Count'),
                'attribute' => 'ticket_count',
            ], 
            [
                'label' => Yii::t('app', 'Account'),
                'content' => function($model, $key, $index, $column) {
                        return Html::a('<span class="glyphicon glyphicon-usd"></span> ' . Yii::t('app', 'Account'), ['/checkout/payment/current-account', 'customer' => $model['customer_id']], ['class' => 'btn btn-sm btn-default']);
                    },
            ],        
            [
                'class' => 'app\components\grid\ActionColumn',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                                Url::to(['/sale/contract/contract/view', 
                                'id'=> $model['contract_id']]), ['class' => 'btn btn-view']);
                    },
                    'ticket' => function($url, $model, $key){
                        return Html::a('<span class= "glyphicon glyphicon-tags"></span>', 
                                Url::to(['/sale/customer/customer-tickets', 
                                'id'=> $model['customer_id']]), ['class' => 'btn btn-warning']);
                    },
                ],
                'template' => '{view} {ticket}  '
                          
            ]
        ]
    ])
        
    ?>
    
    
</div>



