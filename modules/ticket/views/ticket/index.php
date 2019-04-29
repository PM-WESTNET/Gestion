<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\modules\ticket\TicketModule;
use yii\bootstrap\Collapse;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tickets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index padding-full">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?=
            Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
                        'modelClass' => 'Ticket',
                    ]), ['create'], ['class' => 'btn btn-success'])
            ;
            ?>
        </p>
    </div>

    <?php
    Pjax::begin();

    $columns = [

        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'document',
            'header' => TicketModule::t('app', 'ID Card number'),
            'value' => function($model) {
                if (!empty($model->customer))
                    return $model->customer->document_number;
            }
        ],
        [
            'attribute' =>'customer.code',
            'header'=> Yii::t('app', 'Customer Number'),
            'value'=>  function ($model){
                if (!empty($model->customer)) {
                    return $model->customer->code;
                }
            }
        ],
        [
            'attribute' => 'customer',
            'header' => TicketModule::t('app', 'Customer'),
            'value' => function($model) {
                if (!empty($model->customer))
                    return $model->customer->name . ' ' . $model->customer->lastname;
            }
        ],
        [
            'attribute' => 'color_id',
            'format' => 'raw',
            'header' => TicketModule::t('app', 'Color'),
            'value' => function($model) {
                if (!empty($model->color) && !empty($model->number))
                    return "<span class='label label-default' style='background-color: " . $model->color->color . "'>" . $model->color->name . ' (' . $model->number . ")</span>";
            }
        ],
        [
            'header' => TicketModule::t('app', 'Category'),
            'attribute' => 'category_id',
            'value' => function($model) {
                if (!empty($model->category))
                    return $model->category->name;
            }
        ],
        [
            'header' => TicketModule::t('app', 'Status'),
            
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
        ],
    ];
    ?>
    <?php
        $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters');
        
        echo Collapse::widget([
            'items' => [
                [
                    'label' => $item,
                    'content' => $this->render('_filters', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);
        ?>

    <div class="container-fluid no-padding no-margin">
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'id' => 'grid',
            'responsive' => true,
            'hover' => true,
            'rowOptions' => function($model) {
                if ($model->statusIsActive()) {
                    return ['class' => 'font-italic font-bold'];
                }
            }
        ]);
        ?>
    </div>

    <?php Pjax::end(); ?>

</div>
