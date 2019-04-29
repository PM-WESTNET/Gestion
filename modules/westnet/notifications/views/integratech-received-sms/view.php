<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\IntegratechReceivedSms */

$this->title = $model->integratech_received_sms_id;
$this->params['breadcrumbs'][] = ['label' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'Integratech Received Sms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integratech-received-sms-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->integratech_received_sms_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->integratech_received_sms_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'integratech_received_sms_id',
            'destaddr:ntext',
            'charcode:ntext',
            'sourceaddr:ntext',
            'message:ntext',
            'customer_id',
            'ticket_id',
            'datetime',
        ],
    ]) ?>

</div>
