<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . \app\modules\agenda\AgendaModule::t('app', 'Create Task', [
            ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    
    
    <?php \yii\widgets\Pjax::begin(); ?>
    
    <?php 
    $columns = [

        //['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'task_id',            
            'filterInputOptions' => [
                'class' => 'form-control',
                'style' => 'width: 40px;'
            ],
        ],
        [
            'header' => \app\modules\agenda\AgendaModule::t('app', 'Task type'),
            'attribute' => 'task_type_id',
            'filter' => yii\helpers\ArrayHelper::map(app\modules\agenda\models\TaskType::find()->all(), 'task_type_id', 'name'),
            'value' => function($model) {
                if (!empty($model->taskType))
                    return $model->taskType->name;
            }
        ],
        [
            'header' => \app\modules\agenda\AgendaModule::t('app', 'Status'),
            'attribute' => 'status_id',
            'filter' => yii\helpers\ArrayHelper::map(app\modules\agenda\models\Status::find()->all(), 'status_id', 'name'),
            'value' => function($model) {
                if (!empty($model->status))
                    return $model->status->name;
            },
            'contentOptions' => function($model, $key, $index, $column){
                $status = $model->status->slug;
                return [
                    'class' => "task task-on-table task-$status"
                ];
            }
        ],
        [
            'header' => \app\modules\agenda\AgendaModule::t('app', 'Priority'),
            'attribute' => 'priority',
            'filter' => app\modules\agenda\models\Task::getPriorities(),
            'value' => function($model) {
                if (!empty($model->priority))
                    return $model->getPriority();
            }   
        ],
        [
            'attribute' => 'name',
            'value' => 'name',
            'contentOptions' => function($model, $key, $index, $column){
                $expirationStatus = (time() > strtotime($model->date)) ? 'expired' : 'normal' ;
                return [
                    'class' => "task task-on-table task-expiration-$expirationStatus"
                ];
            }
        ],
        [
            'attribute' => 'date',
            'value' => 'date',
            'filter' => kartik\date\DatePicker::widget([
                'model' => $searchModel,
                'attribute'=>'date',
                'type' => kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd',
                ]
            ]),
            'contentOptions' => function($model, $key, $index, $column){
                $expirationStatus = (time() > strtotime($model->date)) ? 'expired' : 'normal' ;
                return [
                    'class' => "task task-on-table task-expiration-$expirationStatus"
                ];
            },
            'format' => 'html',
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
        ],
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,        
        'id'=>'grid',
        'filterModel' => $searchModel,
        'filterSelector'=>'.filter',
    ]); ?>    
    
    <?php \yii\widgets\Pjax::end(); ?>

</div>
