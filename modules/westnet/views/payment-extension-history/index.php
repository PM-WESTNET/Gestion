<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\PaymentExtensionHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment extension history');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-extension-history-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="payment-search">
        <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters'),
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
                'value' => function ($model) {
                    return Html::a($model->customer_id ? $model->customer->fullName : '' , ['/sale/customer/view' , 'id' => $model->customer_id]);
                },
                'format' => 'raw'
            ],
            'from',
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return (new \DateTime('now'))->setTimestamp($model->created_at)->format('d-m-Y H:i');
                }
            ],
        ],
    ]); ?>


</div>
