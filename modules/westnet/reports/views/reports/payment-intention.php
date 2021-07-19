<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;

$this->title = 'Intenciones de Pago';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-intention-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <hr>
    </div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer',
                'format' => 'raw',
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model){
                    return Html::a($model->customer->lastname . ' ' . $model->customer->name . ' (' .$model->customer->code . ')', 
                                ['/sale/customer/view', 'id' => $model->customer->customer_id], 
                                ['class' => 'profile-link']);
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app','Status'),
                'format' => 'raw',
                'filter'=>[
                    'payed'=>Yii::t('app','Pagado'),
                    'pending'=>Yii::t('app','Pendiente'),
                    'canceled'=>Yii::t('app','Cancelado'),
                    'error'=>Yii::t('app','Error'),
                ],
                'value' => function($model){
                    if($model->status == 'payed')
                        return '<span class="label label-success">Pagado</span>';

                    else if($model->status == 'pending')
                        return '<span class="label label-warning">Pendiente</span>';
                    
                    else if($model->status == 'canceled')
                        return '<span class="label label-danger">Cancelado</span>';
                    else
                        return '<span class="label label-danger">Error</span>';
                }
            ],
            'estado',
            [
                'attribute' => 'company',
                'label' => Yii::t('app','Company'),
                'format' => 'raw',
                'value' => function($model){
                    if(!$model->company_id)
                        return null;
                    return $model->company->name;
                }
            ],
            [
                'attribute' => 'payment_id',
                'label' => Yii::t('app','payment'),
                'format' => 'raw',
                'value' => function($model){
                    if(!$model->payment_id)
                        return null;
                    return Html::a('Pago N° '.$model->payment_id , 
                                ['/checkout/payment/view', 'id' => $model->payment_id], 
                                ['class' => 'profile-link']);
                }
            ],
            [
                'attribute'=>'createdAt',
                'label' => 'Fecha',
                'filter'=>DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'from_date',
                    'value' => '2014-01-01',
                    'type' => DatePicker::TYPE_RANGE,
                    'attribute2' => 'to_date',
                    'value2' => '2016-01-01',
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ])
            ], 
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['reports-company/payment-intention-view', 'id' => $model->siro_payment_intention_id], ['data-pjax' => '0']);
                    }
                ]   
            ]
        ],

    ]); ?>

</div>
