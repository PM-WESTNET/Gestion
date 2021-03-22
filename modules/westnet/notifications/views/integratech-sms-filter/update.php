<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\IntegratechSmsFilter */

$this->title = \app\modules\westnet\notifications\NotificationsModule::t('app','Update Integratech Sms Filter').': ' . $model->integratech_sms_filter_id;
$this->params['breadcrumbs'][] = ['label' => \app\modules\westnet\notifications\NotificationsModule::t('app','Integratech Sms Filters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->integratech_sms_filter_id, 'url' => ['view', 'id' => $model->integratech_sms_filter_id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="integratech-sms-filter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
