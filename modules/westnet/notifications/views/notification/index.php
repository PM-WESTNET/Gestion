<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\notifications\models\Transport;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = NotificationsModule::t('app', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-index">
    <div class="name title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?=
            Html::a("<span class='glyphicon glyphicon-plus'></span> " . NotificationsModule::t('app', 'Create Notification'), ['create'], ['class' => 'btn btn-success'])
            ;
            ?>
        </p>
    </div>

    <?php
    \yii\widgets\Pjax::begin();

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        'notification_id',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \app\modules\westnet\notifications\models\Notification::staticFetchStatuses(),
            'header' => NotificationsModule::t('app', 'Status'),
            'value' => function($model) {
                if (!empty($model->status))
                    return $model::staticFetchStatuses()[$model->status];
            }
        ],
        [
            'attribute' => 'transport_id',
            'format' => 'raw',
            'filter' => ArrayHelper::map(Transport::getAllEnabled(), 'transport_id', 'name'),
            'header' => NotificationsModule::t('app', 'Transport'),
            'value' => function($model) {
                if (!empty($model->transport))
                    return $model->transport->name;
            }
        ],
        'name',
        'from_date',
        'from_time',
        [
            'class' => 'app\components\grid\ActionColumn',
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
