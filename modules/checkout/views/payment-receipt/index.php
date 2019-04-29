<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\checkout\models\PaymentReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Receipts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-receipt-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'payment_receipt_id',
            [
                'attribute' => 'amount',
                'format' => ['currency']
            ],
            'date:date',
            'time',
            'concept',
            // 'balance',
            // 'datetime:datetime',
            // 'customer_id',

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>

</div>
