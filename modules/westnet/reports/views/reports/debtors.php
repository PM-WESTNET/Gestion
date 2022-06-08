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

    <h2 class="text-danger"><?= Yii::t('app', 'Total Debt')?> : <?php $total= (double)Yii::$app->session->get('totalDebtors'); echo Yii::$app->formatter->asCurrency($total)?></h2>
    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => 'Nombre',
            'format' => 'html',
            'value' => function($model){
                return Html::a(
                    $model['name'],
                    yii\helpers\Url::toRoute([
                        '/sale/customer/view', 
                        'id' => $model['customer_id'],
                    ])
                );
            }
        ],
        [
            'label' => 'Contrato Estado Actual',
            'format' => 'html',
            'value' => function($model){
                return Html::a(
                    $model['status'],
                    yii\helpers\Url::toRoute([
                        '/sale/contract/contract/view', 
                        'id' => $model['contract_id'],
                    ])
                );
            }
        ],
        [
            'label' => 'Documento',
            'attribute' => 'document',
        ],
        [
            'label' => 'Tipo de Operacion',
            'value' => function($model){
                return 'INCUMPLIMIENTO DE CONTRATO';
            }
        ],
        [
            'label' => 'numero de operacion',
            'attribute' => 'code',
        ],
        [
            'label'=>'importe',
            'format'=>'currency',
            'value' => 'currency',
        ],
        [
            'label' => 'FECHA EN LA CUAL ENTRO EN MORA',
            'attribute'=>'phone',
        ],

        [
            'label' => 'Telefonos',
            'value' => function($model){
                $phones = (!empty($model['phone']))?$model['phone'].' - ':'';
                $phones .= (!empty($model['phone2']))?$model['phone2'].' - ':'';
                $phones .= (!empty($model['phone3']))?$model['phone3'].' - ':'';
                $phones .= (!empty($model['phone4']))?$model['phone4'].' - ':'';
                return $phones;
            },
        ],

        [
            // 'attribute'=> 'debt_bills',
            'label'=>Yii::t('app', 'Debt Bills'),
            'format' => 'html',
            'value' => function($model){
                return Html::a(
                    $model['debt_bills'],
                    yii\helpers\Url::toRoute([
                        '/checkout/payment/current-account', 
                        'customer' => $model['customer_id'],
                    ])
                );
            }
        ]
    ];

    $grid = GridView::begin([
        'dataProvider' => $dataProvider,
        'id'=>'grid',
        'options' => ['class' => 'table-responsive'],                
        'columns' => $columns,
        // 'rowOptions'=>function ($model){
        //     $style = ($model['saldo'] == 0) ? ['style' => 'display:none'] : null;
        //     return $style;
        // },
    ]); ?>

    <?php $grid->end(); ?>
</div>