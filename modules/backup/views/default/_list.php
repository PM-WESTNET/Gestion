<?php

use yii\helpers\Html;

//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\Url;


echo GridView::widget([
    'id' => 'install-grid',
    'export' => false,
    'dataProvider' => $dataProvider,
    'resizableColumns' => false,
    'showPageSummary' => false,
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'responsive' => true,
    'hover' => true,
    'panel' => [
        'heading' => '<h3 class="panel-title"> '.Yii::t('backup','Database backup files').'</h3>',
        'type' => 'default',
        'showFooter' => false
    ],
    // set your toolbar
    'toolbar' => [
        ['content' =>
            Html::a('<i class="glyphicon glyphicon-plus"></i>  '.Yii::t('backup','Create backup'), ['create'], ['class' => 'btn btn-success create-backup margin-right-half ']). ' '.
            Html::a('<i class="glyphicon glyphicon-plus"></i>  '.Yii::t('backup','Upload backup file'), ['upload'], ['class' => 'btn btn-warning ']),
        ],
    ],
    'columns' => array(
        [
            'attribute' => 'name',
            'header' => Yii::t('backup','Name'),
        ],
        [
            'attribute' => 'size',
            'format' => 'size',
            'header' => Yii::t('backup', 'Size'),
        ],
        [
            'attribute' => 'create_time',
            'header' => Yii::t('backup','Create time'),
        ],
        [
            'attribute' => 'modified_time',
            'header' => Yii::t('backup','Modified time'),
            'format' => 'relativeTime',
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{download_action}',
            'header' => Yii::t('app','Download'),
            'buttons' => [
                'download_action' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-download"></span>', $url, [
                                'title' => Yii::t('backup', 'Download this backup'),
                    ]);
                }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'download_action') {
                    $url = Yii::$app->urlManager->createUrl(['backup/default/download', 'file' => $model['name']]);
                    return $url;
                }
            }
                ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{restore_action}',
            'header' => Yii::t('app','Restore'),
            'buttons' => [
                'restore_action' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-import"></span>', $url, [
                                'title' => Yii::t('backup', 'Restore this backup'),
                                'data-confirm' => Yii::t('backup', 'Are you sure you want to restore this backup?'),
                                'data-method' => 'post',
                    ]);
                }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'restore_action') {
                    $url = Yii::$app->urlManager->createUrl(['backup/default/restore', 'file' => $model['name']]);
                    return $url;
                }
            }
                ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{delete_action}',
            'header' => Yii::t('app','Delete'),
            'buttons' => [
                'delete_action' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('backup', 'Delete this backup'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this backup?'),
                                'data-method' => 'post',
                    ]);
                }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                if ($action === 'delete_action') {
                    $url = Yii::$app->urlManager->createUrl(['backup/default/delete', 'file' => $model['name']]);
                    return $url;
                }
            }
                ],        
    ),
]);
?>