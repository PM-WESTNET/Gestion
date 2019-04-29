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

    <h1><?= Html::encode($this->title) ?></h1>
    <h3><?= Yii::t('app', 'Transport') ?>: <?= $model->transport->name . ($model->emailTransport ? " - " . $model->emailTransport->name : '' )  ?></h3>

    <?=
    $this->render('_form-content', [
        'model' => $model,
    ])
    ?>

</div>
