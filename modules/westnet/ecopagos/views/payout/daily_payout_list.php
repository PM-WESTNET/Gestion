<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Cashier;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = EcopagosModule::t('app', 'Payout list');
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Daily closures'), 'url' => ['daily-closure/index']];
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Daily closure') . ' ' . $searchModel->batch_closure_id, 'url' => ['daily-closure/view', 'id' => $searchModel->daily_closure_id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="payout-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        
    </p>

    <?php
    \yii\widgets\Pjax::begin();

    $columns = [
        'payout_id',
        'customer_id',
        [
            'attribute' => 'cashier_id',
            'format' => 'raw',
            'filter' => false,
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
            }
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template' => '{view}',
        ]
    ];

    echo GridView::widget([
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