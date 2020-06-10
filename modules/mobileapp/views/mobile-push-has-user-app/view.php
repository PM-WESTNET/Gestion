<?php

use app\modules\westnet\reports\ReportsModule;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\mobileapp\v1\models\AppFailedRegisterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Notificaciones push');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mobile-push-has-user-app-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Cliente',
                'value' => function($model){
                    return Html::a($model->customer->fullName, ['/sale/customer/view', 'id' => $model->customer->customer_id]);
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Campaña',
                'value' => function($model){
                    return Html::a($model->mobilePush->notification->name, ['/mobileapp/mobile-push/view', 'mobile_push_id' => $model->mobile_push_id]);
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Título',
                'value' => function($model){
                    return $model->notification_title;
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Contenido',
                'value' => function($model){
                    return $model->notification_content;
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Abierta/Leída',
                'value' => function($model){
                    return $model->notification_read;
                },
                'format' => 'boolean'
            ],
            [
                'label' => 'Fecha de envío',
                'value' => function($model) {
                    if($model->sent_at){
                        return (new \DateTime())->setTimestamp($model->sent_at)->format('H:i d-m-Y');
                    }
                }
            ],
        ],
    ]) ?>

</div>
