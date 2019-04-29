<?php

use app\modules\ticket\TicketModule;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */
$this->title= Yii::t('app', 'Tickets from') . $customer->fullName;

?>

<div class="customer-tickets">
    
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

        <p>
            <?=
            Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Create {modelClass}', [
                        'modelClass' => Yii::t('app', 'Ticket'),
                    ]), yii\helpers\Url::to(['/ticket/ticket/create']).'&customer_id='.$customer->customer_id, ['class' => 'btn btn-success'])
            ?>
        </p>
    </div>
    
    <?php 
    
        $columns = [

        ['class' => 'yii\grid\SerialColumn'],
        
        [
            'attribute' => 'color_id',
            'format' => 'raw',
            'header' => Yii::t('app', 'Color'),
            'value' => function($model) {
                if (!empty($model->color) && !empty($model->number))
                    return "<span class='label label-default' style='background-color: " . $model->color->color . "'>" . $model->color->name . ' (' . $model->number . ")</span>";
            }
        ],
        [
            'header' => Yii::t('app', 'Category'),
            'attribute' => 'category_id',
            'value' => function($model) {
                if (!empty($model->category))
                    return $model->category->name;
            }
        ],
        [
            'header' => Yii::t('app', 'Status'),
            
            'attribute' => 'status_id',
            'value' => function($model) {
                if (!empty($model->status))
                    return $model->status->name;
            }
        ],
        [
            'attribute' => 'title',
        ],
        [
            'attribute' => 'assignations',
            'value' => function($model) {
                if (!empty($model->status))
                    return implode(', ', $model->fetchAssignations());
            }
        ],
        [
            'attribute' => 'start_date',
            'value' => 'start_date',
            
        ],
        [
            'attribute' => 'finish_date',
            'value' => 'finish_date',
            
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'buttons' => [
                'view' => function($url, $model, $key){
                    return \yii\bootstrap\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                            yii\helpers\Url::to(['/ticket/ticket/view']).'&id='.$model->ticket_id, ['class' => 'btn btn-view']);
                },
                'update' => function($url, $model, $key){
                    return \yii\bootstrap\Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
                            yii\helpers\Url::to(['/ticket/ticket/update']).'&id='.$model->ticket_id, ['class' => 'btn btn-primary']);
                },
                
            ],
            'template' => '{view} {update}',
        ],
    ];
    ?>
   

    <div class="container-fluid no-padding no-margin">
        <?=
        GridView::widget([
            'dataProvider' => $tickets,
            'columns' => $columns,
            'id' => 'grid',
            //'responsive' => true,
           // 'hover' => true,
            'rowOptions' => function($model) {
                if ($model->statusIsActive()) {
                    return ['class' => 'font-italic font-bold'];
                }
            }
        ]);
        ?> 
    
</div>

