<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker;

$this->title = 'Cambios de razones sociales';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><i class="fa fa-address-book"></i> <?= Html::encode($this->title) ?></h1>
<div class="x_panel">
    <div class="x_content">
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'customer.code',
                'value' => function($model){
                    return ($model->customer ? $model->customer->code:null);
                }
            ],
            [   
                'attribute' => 'customer.name',
                'value' => function($model){
                    return ($model->customer ? $model->customer->name:null);
                }
            ],
            'new_business_name',
            'old_business_name',
            [
                'attribute'=>'date',
                'value' =>'date',
                'filter'=>DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'value' => '2014-01-01',
                    'type' => DatePicker::TYPE_RANGE,
                    'attribute2' => 'date2',
                    'value2' => '2016-01-01',
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ])
            ], 
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>

     <div class="form-group text-center" id="buttonIndex">

        <?=Html::a('<span class="fa fa-reply"></span> Volver', ['/'], ['data-pjax' => '0', 'class' => 'btn btn-warning']);?>

    </div>
</div> 