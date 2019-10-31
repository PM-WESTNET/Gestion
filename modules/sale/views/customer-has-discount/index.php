<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Discounts applied to {customer}', ['customer' => $customer->name]);

$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/sale/customer/view', 'id'=> $customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;
?>
<div class="customer-has-discount-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app', 'Discount to Customer'),
            ]), ['create', 'customer_id' => $customer->customer_id ], ['class' => 'btn btn-success'])
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'discount.name',
            'discount.periods',
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return Yii::t('app',  ucfirst($model->status));
                }
            ],
            [
                'label' => Yii::t('app', 'Value'),
                'value' => function($model){
                    $fixed = ($model->discount->type==\app\modules\sale\models\Discount::TYPE_FIXED);
                    return ( $fixed ? "$ " : "" ) . $model->discount->value .(!$fixed ? "%" : "" );
                }
            ],
            'from_date:date',
            [
                'attribute' => 'to_date',
                'value' => function($model) {
                    return $model->to_date ? $model->to_date : '';
                },
                'format' => 'raw'
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
