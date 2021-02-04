<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Events');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

   <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . \app\modules\agenda\AgendaModule::t('app', 'Create {modelClass}', [
                'modelClass' => 'Event',
            ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
   </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'event_id',
            'task_id',
            'user_id',
            [
                'header'=> 'EventType',
                'value'=>function($model){ if(!empty($model->eventType)) return $model->eventType->name; }
            ],        
                                'date',
            'time',
            // 'datetime',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
