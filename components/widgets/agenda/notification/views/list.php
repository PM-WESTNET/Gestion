<?php
use app\components\widgets\agenda\notification\NotificationBundle;
use app\components\widgets\agenda\AgendaBundle;
use app\components\helpers\UserA;

//use app\components\widgets\notification\assets\NotificationBundle;
NotificationBundle::register($this);
AgendaBundle::register($this);
?>

<div class="notification-list">   

    <div class="row no-padding no-margin text-right">

        <div class="padding-quarter display-inline-block">

            <div class="btn-group btn-group-xs" role="group">
                <button data-notification="mark-all-as-read" type="button" class="btn btn-success"><?= Yii::t('app', "Mark all as read"); ?></button>
            </div>

            <div class="btn-group btn-group-xs" role="group">
                <button data-notifications="close" type="button" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></button>
            </div>

        </div>

    </div>

    <?php if (!empty($notifications)) : ?>

        <?php foreach ($notifications as $notification) : ?>

            <?php $task = $notification->task; ?>

            <div data-notification-container="<?= $notification->notification_id; ?>" data-notification-status="<?= $notification->status; ?>" class="task task-default task-<?= $notification->reason; ?> margin-quarter">

                <div class="row">

                    <div class="col-md-9 col-sm-9">
                        <a href="<?= yii\helpers\Url::to(['/agenda/task/update', 'id' => $notification->task->task_id], true); ?>">
                            <span class="label label-default margin-right-quarter">
                                <?= Yii::$app->formatter->asDatetime($notification->datetime); ?>
                            </span>
                        </a>
                        <a href="<?= yii\helpers\Url::to(['/agenda/task/update', 'id' => $notification->task->task_id], true); ?>">
                            <span class="label label-<?= $notification->reason; ?>">
                                <?= \app\modules\agenda\AgendaModule::t('app', $notification->reason); ?>
                            </span>
                        </a>
                        <a class="hidden-xs" href="<?= yii\helpers\Url::to(['/agenda/task/update', 'id' => $notification->task->task_id], true); ?>">
                            <span class="notification-name padding-left-quarter"><?= $notification->task->name; ?></span>
                        </a>
                    </div>

                    <div class="col-xs-12 visible-xs">
                        <a href="<?= yii\helpers\Url::to(['/agenda/task/update', 'id' => $notification->task->task_id], true); ?>">
                            <span class="notification-name padding-left-quarter"><?= $notification->task->name; ?></span>
                        </a>
                    </div>

                    <div class="col-sm-3">
                        <div class="btn-group btn-group-xs pull-right" role="group">
                            <a type="button" class="btn btn-<?= $notification->task->status->slug; ?>" disabled>
                                <?= $notification->task->status->name; ?>
                            </a>
                            <a data-notification="mark-as-read" data-status="<?= app\modules\agenda\models\Notification::STATUS_READ; ?>" data-id="<?= $notification->notification_id; ?>" type="button" class="btn btn-info" title="<?= Yii::t('app', "Mark as read"); ?>">
                                <span class="glyphicon glyphicon-ok"></span>
                            </a>
                            <a data-notification="mark-as-unread" data-status="<?= app\modules\agenda\models\Notification::STATUS_UNREAD; ?>" data-id="<?= $notification->notification_id; ?>" type="button" class="btn btn-info" title="<?= Yii::t('app', "Mark as unread"); ?>">
                                <span class="glyphicon glyphicon-repeat"></span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        <?php endforeach; ?>
    
        <div class="row">
            <div class="col-lg-12" style="text-align:center;">
                <?= UserA::a('<span class="glyphicon glyphicon-list-alt"></span> '.Yii::t('app','View all'). " ($newNotifications)", ['/agenda/notification/index'], ['class' => 'btn btn-default btn-lg']); ?>
                <?= UserA::a('<span class="glyphicon glyphicon-tasks"></span> '.\app\modules\agenda\AgendaModule::t('app','View tasks'), ['/agenda/task/index'], ['class' => 'btn btn-default btn-lg']); ?>
                <?= UserA::a('<span class="glyphicon glyphicon-calendar"></span> '.Yii::t('app', 'My agenda'), ['/agenda'], ['class' => 'btn btn-default btn-lg']); ?>
            </div>
        </div>
    
    <?php else : ?>

        <p class="text-center text-muted">No hay notificaciones!</p>

    <?php endif; ?>

</div>

<?php
$this->registerJs("Notification.init();", yii\web\View::POS_END);
$this->registerJs("Notification.setChangeStatusUrl('" . yii\helpers\Url::to(['/agenda/notification/change-status'], true) . "');", yii\web\View::POS_END);
$this->registerJs("Notification.setBatchChangeStatusUrl('" . yii\helpers\Url::to(['/agenda/notification/batch-change-status'], true) . "');", yii\web\View::POS_END);
$this->registerJs("Notification.setNotificationCount(" . $newNotifications . ");", yii\web\View::POS_END);
?>