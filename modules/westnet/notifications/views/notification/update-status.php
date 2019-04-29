<?php

use yii\helpers\Html;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */

$this->title = NotificationsModule::t('app', 'Notification') . ' | ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->notification_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="notification-update">

    <h3><?= Yii::t('app', 'Activate') ?>: <?= $model->name ?></h3>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default" style="margin-top: 40px;">
        <div class="panel-body" style="text-align: center; padding: 60px;">
            <p class="lead">
                <?php
                if($model->status != 'enabled'){
                    echo NotificationsModule::t('app', 'Do you want to activate this notification?');
                }else{
                    echo NotificationsModule::t('app', 'Do you want to deactivate this notification?');
                }
                ?>
            </p>

            <?= $this->render('_form-status', ['model' => $model]) ?>
            
        </div>
    </div>

</div>
