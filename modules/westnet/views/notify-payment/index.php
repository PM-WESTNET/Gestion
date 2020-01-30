<?php

use dosamigos\chartjs\ChartJs;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\NotifyPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Notify payments');
$this->params['breadcrumbs'][] = $this->title;
$payment_method_tranferencia = PaymentMethod::getTransferencia();
if($payment_method_tranferencia) {
    $payment_method_tranferencia_id = $payment_method_tranferencia->payment_method_id;
} else {
    $payment_method_tranferencia_id = 0;
}

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
            'from',
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}{verify}',
                'buttons' => [
                    'verify' => function($url, $model, $key) use ($payment_method_tranferencia_id) {
                        if($payment_method_tranferencia_id == $model->payment_method_id && $model->verified == 0) {
                            return Html::a(Yii::t('app', 'Mark as verified'), ['/westnet/notify-payment/verify', 'notify_payment_id' => $key], [
                                'class' => 'btn btn-warning',
                                'data-confirm' => Yii::t('app', 'Are you sure you want to mark it as verified?')
                            ]);
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
