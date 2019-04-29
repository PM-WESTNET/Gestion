<?php

use app\modules\checkout\models\PaymentPlan;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\checkout\models\search\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Plans');
$this->params['breadcrumbs'][] = ['label' => 'Payment Plans', 'url' => ['/checkout/payment/current-account']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-plan-list">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

    </div>

    <div class="filters">

        <?php
        error_log('antes de collapse');
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');

        echo \yii\bootstrap\Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_filters', ['search' => $search]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);

        error_log('pase collapse');

        ?>


    </div>

    <?php

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Customer Number'),
                'value' => function ($model){
                    return $model->customer->code;
                }
            ],
            [
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model){
                    return $model->customer->getFullName();
                }
            ],
            'from_date:date',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function($model) {
                    return Yii::t('app', ucfirst($model->status));
                },
            ],
            'original_amount:currency',
            'payment_plan_amount:currency',
            'fee',
            [
                'label' => Yii::t('app', 'Apply'),
                'value' => function($model) {
                    return ($model->apply  == 0 ? '' : ($model->apply==-1 ? Yii::t('app', 'Discount') : Yii::t('app', 'Surcharge') ) );
                },
            ],
            [
                'label' => Yii::t('app', 'Value Applied'),
                'value' => function($model) {
                    return $model->value_applied ." %";
                },
            ],
            'final_amount:currency',
            'balance:currency',
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view}{cancel}',
                'buttons'=>[
                    'cancel'=>function ($url, $model, $key) {
                        if($model->status == PaymentPlan::STATUS_ACTIVE) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                yii\helpers\Url::toRoute(['/checkout/payment-plan/cancel', 'id' => $model->payment_plan_id, 'customer_id' => $model->customer_id]),
                                [
                                    'title' => Yii::t('yii', 'Cancel'),
                                    'class' => 'updateItem btn btn-warning'
                                ]);
                        }
                    },
                ]
            ]
        ],
    ]); ?>

</div>
