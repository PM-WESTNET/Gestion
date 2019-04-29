<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = EcopagosModule::t('app', 'Batch Closures');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-closure-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?=
        Html::a("<span class='glyphicon glyphicon-plus'></span> " . EcopagosModule::t('app', 'Create batch closure'), ['create'], ['class' => 'btn btn-success'])
        ;
        ?>
    </p>

    <?php
    \yii\widgets\Pjax::begin();

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'header' => EcopagosModule::t('app', 'Ecopago'),
            'value' => function($model) {
                if (!empty($model->ecopago))
                    return $model->ecopago->name;
            }
        ],
        'batch_closure_id',
        [
            'attribute' => 'collector_id',
            'format' => 'raw',
            'filter' => UserHelper::getEcopago()->fetchCollectors(true),
            'header' => EcopagosModule::t('app', 'Collector'),
            'value' => function($model) {
                if (!empty($model->collector))
                    return $model->collector->getFormattedName();
            }
        ],
        'datetime:datetime',
        'payment_count',
        'total:currency',
        'commission:currency',
        'discount:currency',
        [
            'header' => EcopagosModule::t('app', 'Net total'),
            'value' => function($model) {
                if (!empty($model->netTotal))
                    return Yii::$app->formatter->asCurrency($model->netTotal);
            }
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \app\modules\westnet\ecopagos\models\BatchClosure::staticFetchStatuses(),
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
