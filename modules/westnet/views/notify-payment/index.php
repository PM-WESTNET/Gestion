<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\NotifyPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Notify payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notify-payment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="payment-search">
        <?php
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

        echo Collapse::widget([
        'items' => [
        [
        'label' => $item,
        'content' => $this->render('_search', ['model' => $searchModel]),
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer_id',
                'value' => function($model) {
                    return Html::a($model->customer->fullName, ['/sale/customer/view', 'id' => $model->customer_id]);

                },
                'format' => 'raw'
            ],
            'date',
            'amount',
            [
                'attribute' => 'payment_method_id',
                'value' => function($model) {
                    return $model->paymentMethod->name;
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>
