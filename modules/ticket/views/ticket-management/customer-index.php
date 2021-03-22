<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;
use app\modules\sale\models\Customer;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\ticket\models\search\TicketManagementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$customer = Customer::findOne($customer_id);
$this->title = Yii::t('app', 'Ticket Managements from') .': '. $customer->fullName;
$this->params['breadcrumbs'][] = ['label' => $customer->fullName, 'url' => ['/sale/customer/view','id' => $customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-management-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

    echo Collapse::widget([
        'items' => [
            [
                'label' => $item,
                'content' => $this->render('_search', ['model' => $searchModel,'customer_id' => $customer_id]),
                'encode' => false,
            ],
        ],
        'options' => [
        'class' => 'hidden-print'
        ]
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'timestamp',
                'value' => function($model) {
                    return $model->timestamp ? (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d-m-Y') : '';
                }
            ],
            [
                'attribute' => 'ticket_id',
                'value' => function($model) {
                    return $model->ticket ? $model->ticket->title : '';
                }
            ],
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    return $model->user ? $model->user->username : '';
                }
            ],
            [
                'attribute' => 'by_wp',
                'value' => function($model) {
                    if($model->by_wp) {
                        return "<span class='glyphicon glyphicon-ok' style='color: #2b542c'></span>";
                    }
                    return "<span class='glyphicon glyphicon-remove' style='color:#9e0505'></span>";

                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'by_call',
                'value' => function($model) {
                    if($model->by_call) {
                        return "<span class='glyphicon glyphicon-ok' style='color: #2b542c'></span>";
                    }
                    return "<span class='glyphicon glyphicon-remove' style='color:#9e0505'></span>";

                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'by_email',
                'value' => function($model) {
                    if($model->by_email) {
                        return "<span class='glyphicon glyphicon-ok' style='color: #2b542c'></span>";
                    }
                    return "<span class='glyphicon glyphicon-remove' style='color:#9e0505'></span>";

                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'by_sms',
                'value' => function($model) {
                    if($model->by_sms) {
                        return "<span class='glyphicon glyphicon-ok' style='color: #2b542c'></span>";
                    }
                    return "<span class='glyphicon glyphicon-remove' style='color:#9e0505'></span>";

                },
                'format' => 'raw'
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{ticket}',
                'buttons' => [
                    'ticket' => function($url, $model) {
                        return Html::a(Yii::t('app', 'See ticket'), ['/ticket/ticket/view', 'id' => $model->ticket_id], [
                            'class' => 'btn btn-primary'
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
