<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\notifications\models\search\IntegratechReceivedSms */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = NotificationsModule::t('app','Integratech Received Sms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integratech-received-sms-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?=$this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'sourceaddr:ntext',
            'message:ntext',
            [
                'label' => NotificationsModule::t('app', 'Customer'),
                'value' => function($model){
                    return $model->customer ? $model->customer->fullName : '';
                }
            ],
            [
                'attribute' => 'datetime',
                'filter' => false
            ],
            [
                'label' => NotificationsModule::t('app', 'Mesa ticket'),
                'value' => function ($model){
                    if($model->ticket_id){
                        return '<span class="glyphicon glyphicon-ok"></span>';
                    } else {
                        return '<span class="glyphicon glyphicon-remove"></span>';
                    }
                },
                'format' => 'raw',
                'filter' => false,
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
//                'template' => '{view}{delete}'
            ],
        ],
    ]); ?>
</div>
