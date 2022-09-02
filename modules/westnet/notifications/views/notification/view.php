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

$this->title = $model->name . ' (' . NotificationsModule::t('app', ucfirst($model->status)) . ')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-view">
    <div class="messages">
        <?php if ($model->status === Notification::STATUS_ERROR && $model->error_msg) : ?>
            <div class="alert alert-danger">
                <?= $model->error_msg ?>
            </div>
        <?php endif; ?>
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
        if ($model->transport->hasFeature('manualSent') && $model->status == Notification::STATUS_ENABLED) {
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
    if ($model->status === 'in_process' || $model->status === 'pending' || $model->status === 'paused') {
        if ($model->transport->slug === 'email') {
            echo $this->render('_email_status', ['model' => $model]);
        } elseif ($model->transport->slug === 'mobile-push') {
            echo $this->render('_mobile_push_status', ['model' => $model]);
        }
    }
    ?>

    <?php $attributes = [
        'notification_id',
        'create_timestamp:datetime',
        'update_timestamp:datetime',
        [
            'attribute' => 'status',
            'value' => NotificationsModule::t('app', ucfirst($model->status))
        ],
        [
            'attribute' => 'status_message',
            'value' => $model->status_message,
        ],
        [
            'attribute' => 'transport_id',
            'value' => $model->transport->name
        ],
        'name',
        [
            'attribute' => 'subject',
            'value' => $model->subject,
            'format' => 'html'
        ],
        [
            'attribute' => 'layout',
            'value' => function($model) {
                return NotificationsModule::t('app', $model->layout);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'content',
            'value' => function($model){
                $content = $model->content;
                // if this notification has a layout set, render a preview button for the layout and
                if(!empty($model->layout)){
                    // render button
                    $content .= Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span> '.NotificationsModule::t('app',"Preview layout").'',
                        ['preview', 'id' =>  $model->notification_id],
                        ['class' => 'btn btn-default', 'target' => '_blank']
                    );
                }
                else{
                    // send flash to the user that no layout was found and which default is set for the notification type
                    if(!empty($model->emailTransport)){
                        $flash = NotificationsModule::t('app', 'This notification has no specific layout and will be sent with {layout}', ['layout' => $model->emailTransport->layout]);
                        Yii::$app->session->addFlash('info', $flash);
                    }else{
                        //no email transport setted ? 
                    }
                }

                return $content;
            },
            'format' => 'raw'
        ],
        'from_date:date',
        'from_time',
        'to_date:date',
        'to_time',
        'times_per_day',
        [
            'attribute' => 'last_sent',
            'value' => Yii::$app->formatter->asDate($model->last_sent),
            'format' => 'html'
        ],
    ];

    $integratech_transport = Transport::findOne(['slug' => 'sms-integratech']);
    if ($model->transport_id == $integratech_transport->transport_id) {
        array_push($attributes, 'test_phone');
        array_push($attributes, [
            'attribute' => 'test_phone_frecuency',
            'value' => function ($model) {
                return 'Envío de mensaje a teléfono de prueba cada ' . $model->test_phone_frecuency . ' mensajes.';
            }
        ]);
    } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]);

    foreach ($model->media as $media) {
        echo Preview::widget(['media' => $media]);
    }
    ?>
</div>

<?php if ($model->status === 'in_process' || $model->status === 'pending' || $model->status === 'paused') : ?>

    <?php
    if ($model->transport->name === 'Email') {
        $this->registerJs('EmailStatus.init()');
    }

    if ($model->transport->name === 'Mobile Push') {
        $this->registerJs('NotificationStatus.init()');
    }

    ?>
<?php endif; ?>