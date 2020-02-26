<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Collapse;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('afip', 'Book ' . ucfirst($type));
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taxes-book-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " .
                Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('afip','Book ' . ucfirst($type))]),
            ['create', 'type'=>$type],
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>
    <div>
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
    ]); ?>
    </div>

    <?php
    $columns[] = ['class' => 'yii\grid\SerialColumn'];
    if (Yii::$app->params['companies']['enabled']) {
        $columns[] = ['class' => 'app\components\companies\CompanyColumn'];
    }
    $columns[] = [
        'header'=> Yii::t('afip', 'Period'),
        'value' => function($model) {
            return Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy');
        }
    ];
    $columns[] = 'number';
    $columns[] = [
        'attribute' => 'status',
        'value' => function($model){
            return Yii::t('afip', $model->status);
        }
    ];
    $columns[] = [
        'class' => 'app\components\grid\ActionColumn',
        'template'=>' {view} {update} {delete} {print} {export-excel} {export-txt-alicuotas} {export-txt-cbt}',
        'buttons'=>[
            'view' => function ($url, $model, $key) {
                return $model->status === 'draft' ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['class' => 'btn btn-view']) :
                    Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['add-'.$model->type."-bills", 'id'=>$key]), ['class' => 'btn btn-view']);
            },
            'update' => function ($url, $model, $key) {
                return $model->status === 'draft' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
            },
            'delete' => function ($url, $model, $key) {
                if($model->getDeletable()){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['taxes-book/delete', 'id'=>$key]), [
                        'title' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '1',
                        'class' => 'btn btn-danger'
                    ]);
                }
            },
            'print' => function ($url, $model, $key) {
                return $model->getTaxesBookItems()->exists() ? Html::a('<span class="glyphicon glyphicon-print"></span>', $url, ['target'=>'_blank', 'class' => 'btn btn-print']) : '';
            },
            'export-excel' => function ($url, $model, $key) {
                return $model->getTaxesBookItems()->exists() ? Html::a('<span class="glyphicon glyphicon-download" data-toggle="tooltip" title="'.Yii::t('afip', 'Download Excel').'"></span>', $url, ['target'=>'_blank', 'class' => 'btn btn-print']) : '';
            },
            'export-txt-alicuotas' => function ($url, $model, $key) {
                return $model->getTaxesBookItems()->exists() ? Html::a('<span class="glyphicon glyphicon-save-file" data-toggle="tooltip" title="'.Yii::t('afip', 'Download Alic TXT').'"></span>', yii\helpers\Url::toRoute(['export-txt', 'id'=>$key, 'type'=>'alicuotas' ]), ['target'=>'_blank', 'class' => 'btn btn-print']) : '';
            },
            'export-txt-cbt' => function ($url, $model, $key) {
                return $model->getTaxesBookItems()->exists() ? Html::a('<span class="glyphicon glyphicon-cloud-upload" data-toggle="tooltip" title="'.Yii::t('afip', 'Download Bills TXT').'"></span>', yii\helpers\Url::toRoute(['export-txt', 'id'=>$key, 'type'=>'cbt']), ['target'=>'_blank', 'class' => 'btn btn-print']) : '';
            },
        ]
    ];


    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns
    ]); ?>

</div>
