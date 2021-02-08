<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\widgets\agenda\notification\NotificationBundle;
use app\components\widgets\agenda\AgendaBundle;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;

NotificationBundle::register($this);
AgendaBundle::register($this);
?>
<div class="notification-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'notification_id',
            [
                'attribute' => 'datetime',
                'value' => function($notification){return Yii::$app->formatter->asDatetime($notification->datetime);}
            ],
            [
                'attribute' => 'reason',
                'value' => function($notification){return \app\modules\agenda\AgendaModule::t('app', $notification->reason);}
            ],
            [
                'attribute' => 'task',
                'value' => function($notification){return \app\components\helpers\UserA::a($notification->task->name, ['/agenda/task/update', 'id' => $notification->task->task_id]);},
                'format' => 'html'
            ],
//            [
//                'attribute' => 'status',
//                'value' => function($notification){
//                    $html = '<div class="btn-group btn-group-xs pull-right" role="group">';
//                    $html .= yii\helpers\Html::a($notification->task->status->name, '#', ['class' => 'btn btn-'.$notification->task->status->slug, 'disabled' => 'disabled']);
//                    $html .= yii\helpers\Html::a('<span class="glyphicon glyphicon-ok"></span>','#',['data' => ['notification' => 'mark-as-read', 'status' => app\modules\agenda\models\Notification::STATUS_READ, 'id' => $notification->notification_id], 'class' => 'btn btn-info']);
//                    $html .= yii\helpers\Html::a('<span class="glyphicon glyphicon-repeat"></span>','#',['data' => ['notification' => 'mark-as-unread', 'status' => app\modules\agenda\models\Notification::STATUS_UNREAD, 'id' => $notification->notification_id], 'class' => 'btn btn-info']);
//                    $html .= '</div>';
//                    
//                    return $html;
//                },
//                'format' => 'raw'
//            ],

            [
                'class' => 'app\components\grid\ActionColumn',
                'controller' => 'task'
            ],
        ],
        'options' => [
            'class' => 'notification-list-grid'
        ],
        'rowOptions' => function ($model, $key, $index, $grid){
            $options = ['data-notification-container' => $model->notification_id];
            if($model->status == \app\modules\agenda\models\Notification::STATUS_UNREAD){
                $options['style'] = 'opacity: 0.75; font-style: italic;';
            }
            return $options;
        }
    ]); ?>

</div>

<?php
$this->registerJs("Notification.init();", yii\web\View::POS_END);
$this->registerJs("Notification.setChangeStatusUrl('" . yii\helpers\Url::to(['/agenda/notification/change-status'], true) . "');", yii\web\View::POS_END);
$this->registerJs("Notification.setBatchChangeStatusUrl('" . yii\helpers\Url::to(['/agenda/notification/batch-change-status'], true) . "');", yii\web\View::POS_END);
?>