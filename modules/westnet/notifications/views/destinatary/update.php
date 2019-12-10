<?php

use yii\helpers\Html;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Destinatary */

$this->title = NotificationsModule::t('app', 'Selection of filters');
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Notifications'), 'url' => ['notification/index']];
$this->params['breadcrumbs'][] = ['label' => $model->notification->name, 'url' => ['notification/view', 'id' => $model->notification->notification_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Destinatary update -->
<div class="destinatary-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
        'notification' => $notification
    ])
    ?>

</div>
<!-- end Destinatary update -->