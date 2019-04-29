<?php

use app\modules\westnet\notifications\NotificationsModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */

$this->title = NotificationsModule::t('app', 'Create Notification');
$this->params['breadcrumbs'][] = ['label' => NotificationsModule::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form-transport', [
        'model' => $model,
    ])
    ?>

</div>
