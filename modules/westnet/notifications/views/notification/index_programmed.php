<?php

use yii\bootstrap\Collapse;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\notifications\models\Transport;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = NotificationsModule::t('app', 'Notifications');

if ($searchModel->programmed) {
    $this->title = NotificationsModule::t('app', 'Programmed Notifications');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notifications'), 'url' => ['index']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-index">
    <div class="name title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . NotificationsModule::t('app', 'Create Notification'), ['create'], ['class' => 'btn btn-success']);?>
            <?php
                if (!$searchModel->programmed) {
                    echo Html::a("<span class='glyphicon glyphicon-clock'></span> " . NotificationsModule::t('app', 'Programmed Notifications'), ['index-programmed'], ['class' => 'btn btn-warning']);
                }
            ?>
        </p>
    </div>
    <div>
        <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                    'content' => $this->render('_search', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);?>
    </div>

    <?php


    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        'notification_id',
        [
            'attribute' => 'name',
            'label' => 'Nombre de campaña',
        ],
        [
            'attribute' => 'create_timestamp',
            'label' => 'Fecha de creación',
            'value' => function($model){
                return $model->create_timestamp ? (new \DateTime())->setTimestamp($model->create_timestamp)->format('d-m-Y') : '';
            },

        ],
        [
            'label' => 'Fecha programada de envío',
            'value' => function($model){
                return $model->getNextProgrammedSend();
            },
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \app\modules\westnet\notifications\models\Notification::staticFetchStatuses(),
            'header' => NotificationsModule::t('app', 'Status'),
            'value' => function($model) {
                if (!empty($model->status))
                    return $model::staticFetchStatuses()[$model->status];
            },
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
        [
            'class' => 'app\components\grid\ActionColumn',
        ]
    ];

    echo GridView::widget([
        'resizableColumns' => true,
        'dataProvider' => $dataProvider,
        'columns' => $columns,
        'id' => 'grid',
        'responsive' => true,
        'hover' => true,
        'resizableColumns' => true,
        'rowOptions' => function($model) {
            
        },
    ]);
    
    ?>

</div>
