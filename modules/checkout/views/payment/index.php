<?php

use app\modules\checkout\models\search\PaymentSearch;
use yii\bootstrap\Collapse;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\modules\checkout\models\Payment;

/* @var $this View */
/* @var $searchModel PaymentSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Payment'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <div class="payment-search">

    <?php
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_payment_filters', ['search' => $searchModel, 'paymentMethods' => $paymentMethods]),
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
    <?php

    $columns[] = ['class' => 'yii\grid\SerialColumn'];
    //Columna de empresa, solo si se encuentra activa la func. de empresas
    if(Yii::$app->params['companies']['enabled']){
        $columns[] = [
            'value' => function($model) {
                return $model->company_name;
            },
            'label' => Yii::t('app', 'Company')
        ];
    }

    $columns = array_merge($columns, [
        [
            'label' => Yii::t('app', 'Customer Number'),
            'value' => 'customer.code'
        ],
        [
            'header' => Yii::t('app','Customer'),
            'attribute' => function($model){ return $model->customer ? Html::a($model->customer->fullName, ['/sale/customer/view', 'id'=>$model->customer_id]) : null; },
            'format' => 'raw'
        ],
        'date:date',
        [
            'attribute' => 'amount',
            'format' => ['currency'],
        ],
        [
            'label' => Yii::t('app', 'Payment Method'),
            'value' => function($model) {return $model->payment_method;},
        ],
        [
            'label' => Yii::t('app', 'Status'),
            'value' => function($model) {
                return Yii::t('app', ucfirst($model->status));
            },
        ],
        [
            'label' => Yii::t('app', 'Bills'),
            'value' => function($model) {
                if ($model->bill_numbers) {
                    return $model->bill_numbers;
                } else {
                    return Yii::t('app', 'Payment to Account');
                }
            }
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{view} {update} {delete} {pdf} {email}',
            'buttons'=>[
                'pdf' => function ($url, $model, $key) {
                    return ($model->status == 'closed' ?
                        Html::a('<span class="glyphicon glyphicon-print"></span>', Url::toRoute(['payment/pdf', 'id'=>$key]), ['target'=>"_blank", 'class' => 'btn btn-print']) : '') ;
                },
                'email' => function ($url, $model, $key) {
                    if($model->status === Payment::PAYMENT_CLOSED && ($model->customer ? trim($model->customer->email) : "" ) !=""){
                        return  Html::a('<span class="glyphicon glyphicon-envelope"></span>', Url::toRoute(['email', 'id' => $key, 'from' => 'index']), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);
                    }
                },
                'delete' => function ($url, $model, $key) {
                    if($model->getDeletable() && $model->status != 'cancelled'){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['payment/cancel-payment', 'id'=>$key]), [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '1',
                            'class' => 'btn btn-danger'
                        ]);
                    }
                },
                'update' => function ($url, $model, $key) {
                    return $model->status == 'draft' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
                },
            ]
        ]
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ]); ?>

</div>
