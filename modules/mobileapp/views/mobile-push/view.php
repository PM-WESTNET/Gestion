<?php

use app\modules\westnet\reports\ReportsModule;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\mobileapp\v1\models\AppFailedRegisterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Notificaciones push');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mobile-push-has-user-app-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Collapse::widget([
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                'content' => $this->render('_search', ['model' => $searchModel]),
                'encode' => false,
            ],
        ],
        'options' => [
            'class' => 'hidden-print'
        ]
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => 'Código de cliente',
                'value' => function($model){
                    return  $model['customer_code'];
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Cliente',
                'value' => function($model){
                    return Html::a($model['customer_name'], ['/sale/customer/view', 'id' => $model['customer_id']]);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Estado de la notificación',
                'value' => function($model){
                    return ReportsModule::t('app', $model['status']);
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Notificación abierta/leída',
                'value' => function($model){
                    return $model['notification_read'];
                },
                'format' => 'boolean',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>' {view}',
                'buttons'=>[
                    'view' => function ($url, $model, $key) {
                        return Html::a('Ver Contenido de la notificación', ['/mobileapp/mobile-push-has-user-app/view', 'id' => $model['mobile_push_has_user_app_id']], ['class' => 'btn btn-info']);
                    },
                ]
            ]
        ]
    ]); ?>

</div>
