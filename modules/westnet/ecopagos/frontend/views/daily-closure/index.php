<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

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
        'daily_closure_id',
        [
            'attribute' => 'cashier_id',
            'format' => 'raw',
            'filter' => UserHelper::getEcopago()->fetchCashiers(true),
            'header' => EcopagosModule::t('app', 'Cashier'),
            'value' => function($model) {
                if (!empty($model->cashier))
                    return $model->cashier->getCompleteName();
            }
        ],
        'datetime:datetime',
        'payment_count',
        'total',
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
