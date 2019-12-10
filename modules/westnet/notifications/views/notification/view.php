<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;
use app\modules\westnet\notifications\models\Notification;
use app\modules\westnet\notifications\models\Transport;
use app\modules\media\components\view\Preview;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */

$this->title = $model->name . ' ('. NotificationsModule::t('app', ucfirst($model->status)) . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-view">
    <div class="messages">
        
    </div>
    <div class="name title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->notification_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-th-list"></span> ' . NotificationsModule::t('app', 'Choose destinataries'), ['destinatary/index', 'notification_id' => $model->notification_id], ['class' => 'btn btn-info']) ?>
            
            <?= Html::a('<span class="glyphicon glyphicon-off"></span> ' . NotificationsModule::t('app', 'Change status'), ['notification/update-status', 'id' => $model->notification_id], ['class' => 'btn btn-info']) ?>
            
            <?php
            if ($model->deletable)
                echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->notification_id], [
                    'class' => 'btn btn-danger pull-right',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
        </p>
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> ' . NotificationsModule::t('app', 'Export'), ['notification/export', 'id' => $model->notification_id], ['class' => 'btn btn-info', 'target' => '_blank']) ?>
            
            <?php
            //Se puede enviar manualmente?
            if($model->transport->hasFeature('manualSent') && $model->status == Notification::STATUS_ENABLED){
                echo Html::a('<span class="glyphicon glyphicon-send"></span> ' . NotificationsModule::t('app', 'Send now'), ['notification/send', 'id' => $model->notification_id, 'force_send' => true], [
                    'class' => 'btn btn-success',
                    'data' => [
                        'confirm' => NotificationsModule::t('app', 'Are you sure you want to send this notification?'),
                        'method' => 'post',
                    ],
                ]);
            }    
            ?>

            <?= Html::a('<span class="glyphicon glyphicon-off"></span> ' . NotificationsModule::t('app', 'Abort send'), ['notification/abort-send', 'notification_id' => $model->notification_id], ['class' => 'btn btn-danger pull-right']) ?>
        </p>

    <?php
        if ($model->status === 'in_process' || $model->status === 'pending') {
            echo $this->render('_email_status', ['model' => $model]);
        }
    ?>

    <?php $attributes = [
            'notification_id',
            'create_timestamp:datetime',
            'update_timestamp:datetime',
            [
                'attribute' => 'transport_id',
                'value' => $model->transport->name
            ],
            'name',
            [
                'attribute' => 'content',
                'value' => $model->layout ? $this->render(LayoutHelper::getLayoutAlias($model->layout), ['content' => $this->render('@app/modules/westnet/notifications/body/content/content', ['notification' => $model])]) : $model->content,
                'format' => 'html'
            ],
            'from_date:date',
            'from_time',
            'to_date:date',
            'to_time',
            'times_per_day',
            [
                'attribute' => 'layout',
                'value' => NotificationsModule::t('app', $model->layout)
            ],
            [
                'attribute' => 'status',
                'value' => NotificationsModule::t('app', ucfirst($model->status))
            ],
            [
                'attribute' => 'last_sent',
                'value' => Yii::$app->formatter->asDate($model->last_sent),
                'format' => 'html'
            ],
    ];

    $integratech_transport = Transport::findOne(['slug' => 'sms-integratech']);
    if($model->transport_id == $integratech_transport->transport_id) {
        array_push($attributes, 'test_phone');
        array_push($attributes, [
            'attribute' => 'test_phone_frecuency',
            'value' => function ($model) {
                return 'Envío de mensaje a teléfono de prueba cada ' .$model->test_phone_frecuency. ' mensajes.';
            }
        ]);
    } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]);

    foreach($model->media as $media){
        echo Preview::widget(['media' => $media]);
    }
    ?>
</div>
