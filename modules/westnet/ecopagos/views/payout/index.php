<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Cashier;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = EcopagosModule::t('app', 'Payouts in Ecopagos');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payout-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>

    </p>

    <?php
    \yii\widgets\Pjax::begin();

    $columns = [
        'payout_id',
        [
            'attribute' => 'ecopago_id',
            'format' => 'raw',
            'filter' => ArrayHelper::map(app\modules\westnet\ecopagos\models\Ecopago::find()->all(), 'ecopago_id', 'name'),
            'header' => EcopagosModule::t('app', 'Ecopago branch'),
            'value' => function($model) {
                if (!empty($model->ecopago))
                    return $model->ecopago->name;
            }
        ],
        [
            'attribute' => 'customer_number',
            'header' => EcopagosModule::t('app', 'Customer number'),
            'value' => 'customer_number'
        ],
        [
            'attribute' => 'customer',
            'header' => EcopagosModule::t('app', 'Customer'),
            'value' => function($model) {
                if (!empty($model->customer))
                    return $model->customer->name . ' ' . $model->customer->lastname;
            }
        ],
        [
            'attribute' => 'cashier_id',
            'format' => 'raw',
            'filter' => ArrayHelper::map(Cashier::find()->all(), 'cashier_id', 'name'),
            'header' => EcopagosModule::t('app', 'Cashier'),
            'value' => function($model) {
                if (!empty($model->cashier))
                    return $model->cashier->name;
            }
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \app\modules\westnet\ecopagos\models\Payout::staticFetchStatuses(),
            'header' => EcopagosModule::t('app', 'Status'),
            'value' => function($model) {
                if (!empty($model->status))
                    return $model->fetchStatuses()[$model->status];
            }
        ],
        'date',
        'time',
        [
            'attribute' => 'batch_closure_id',
            'header' => EcopagosModule::t('app', 'Batch closure'),
            'value' => function($model) {
                if (!empty($model->batchClosure))
                    return $model->batchClosure->batch_closure_id;
            },
            'pageSummary' => 'Total',
            'footer' => true,
        ],
        [
            'attribute' => 'amount',
            'footer' => true,
            'pageSummary' => Yii::$app->formatter->asCurrency($total),
            'format'=>['currency']
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{view}',
            'viewOptions' => ['class' => 'btn btn-view'],
        ]
    ];

    echo GridView::widget([
        'showPageSummary' => true,
        'resizableColumns' => true,
        'dataProvider' => $dataProvider,
        'columns' => $columns,
        'id' => 'grid',
        'filterModel' => $searchModel,
        'filterSelector' => '.filter',
        'responsive' => true,
        'hover' => true,
        'resizableColumns' => true,
        'rowOptions' => function($model) {
            
        },
    ]);

    \yii\widgets\Pjax::end();
    ?>
</div>