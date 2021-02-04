<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('westnet', 'Servers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="server-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('westnet', 'Server'),
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

            'server_id',
            'name',
            [
                'attribute'=>'status',
                'filter'=>[
                    'enabled'=>Yii::t('app','Enabled'),
                    'disabled'=>Yii::t('app','Disabled'),
                ],
                'value'=>function($model){return Yii::t('app',  ucfirst($model->status)); }
            ],
            'class',
            'url',
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view} {update} {delete} {move} {restore}',
                'buttons'=>[
                    'move' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-random"></span>', yii\helpers\Url::toRoute(['server/move-customers', 'id'=>$key]), [
                            'title' => Yii::t('westnet', 'Move Customers'),
                            'class' => 'btn btn-warning'
                        ]);
                    },
                    'restore' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-retweet"></span>', yii\helpers\Url::toRoute(['server/restore-customers', 'id'=>$key]), [
                            'title' => Yii::t('westnet', 'Restore Customers'),
                            'class' => 'btn btn-warning'
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
