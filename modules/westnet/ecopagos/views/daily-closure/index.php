<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Cashier;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = EcopagosModule::t('app', 'Daily closures');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daily-closure-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    \yii\widgets\Pjax::begin();

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'ecopago_id',
            'format' => 'raw',
            'filter' => ArrayHelper::map(app\modules\westnet\ecopagos\models\Ecopago::find()->all(), 'ecopago_id', 'name'),
            'header' => EcopagosModule::t('app', 'Ecopago'),
            'value' => function($model) {
                if (!empty($model->ecopago))
                    return $model->ecopago->name;
            }
        ],
        'daily_closure_id',
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
        'datetime:datetime',
        'payment_count',
        'total',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \app\modules\westnet\ecopagos\models\DailyClosure::staticFetchStatuses(),
            'header' => EcopagosModule::t('app', 'Status'),
            'value' => function($model) {
                if (!empty($model->status))
                    return $model->fetchStatuses()[$model->status];
            }
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template' => '{view}',
        ]
    ];
    ?>

    <?php
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
