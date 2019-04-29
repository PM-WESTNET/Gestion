<?php

use app\modules\westnet\notifications\NotificationsModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Destinatary */

$this->title = NotificationsModule::t('app', 'Search destinataries');
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Notifications'), 'url' => ['notification/index']];
$this->params['breadcrumbs'][] = ['label' => $notification->name, 'url' => ['notification/view', 'id' => $notification->notification_id]];
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Destinataries'), 'url' => ['index', 'notification_id' => $notification->notification_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="destinatary-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
