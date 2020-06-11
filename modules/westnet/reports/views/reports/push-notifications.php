<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\bootstrap\Collapse;
use app\modules\westnet\reports\ReportsModule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\provider\models\search\ProviderBillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Mobile app report')  ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-bill-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'Número de campaña',
                'value' => function($model){
                    return $model['notification_id'];
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Nombre de campaña',
                'value' => function($model) {
                    return Html::a($model['notification_name'], ['/westnet/notifications/notification/view', 'id' => $model['notification_id']]);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Fecha y hora de envío',
                'value' => function($model) {
                    return (new DateTime())->setTimestamp($model['send_timestamp'])->format('H:m d-m-Y');
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Cantidad enviada',
                'value' => function($model) {
                    return $model['count_sent'];
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Cantidad abierta/leída',
                'value' => function($model) {
                    return $model['count_read'];
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Detalles',
                'value' => function($model) {
                    return Html::a('Ver detalles', ['/mobileapp/mobile-push/view', 'mobile_push_id' => $model['mobile_push_id']], ['class' => 'btn btn-info']);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ]

        ]
    ])?>
</div>