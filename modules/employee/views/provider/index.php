<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\provider\models\search\ProviderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Providers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Provider'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'provider_id',
            'name',
            'business_name',
            'tax_identification',
            'address',
            [
                'label' => Yii::t('app','Bills'),
                'value'=> function ($model){
                    return yii\bootstrap\ButtonDropdown::widget([
                        'label' => '<span class="glyphicon glyphicon-list-alt"></span> '. Yii::t('app','Bills'),
                        'dropdown' => [
                            'items' => [
                                '<li>'.Html::a('<span class="glyphicon glyphicon-eye-open"></span> '. 'Ver',
                                    ['provider-bill/index','provider_id'=>$model->provider_id]).'</li>',
                                
                                '<li>'.Html::a('<span class="glyphicon glyphicon-plus"></span> '. 'Nuevo',
                                    ['provider-bill/create', 'provider'=>($model ? $model->provider_id : null )] ).'</li>',
                            ],
                            'encodeLabels'=>false,
                            'options' => ['class' => 'dropdown-menu dropdown-menu-right']
                        ],
                        'encodeLabel' => false,
                        'options'=>[
                                'class'=>isset($class) ? $class : 'btn btn-warning',
                        ]
                        ]
                    );
                },
                'format' => 'raw'
            ],        
            
            [
                'label' => Yii::t('app','Payments'),
                'value'=> function ($model){
                    return yii\bootstrap\ButtonDropdown::widget([
                        'label' => '<span class="glyphicon glyphicon-list-alt"></span> '. Yii::t('app','Payments'),
                        'dropdown' => [
                            'items' => [
                                '<li>'.Html::a('<span class="glyphicon glyphicon-eye-open"></span> '. 'Ver',
                                    ['provider-payment/index','provider_id'=>$model->provider_id]).'</li>',
                                
                                '<li>'.Html::a('<span class="glyphicon glyphicon-plus"></span> '. 'Nuevo',
                                    ['provider-payment/create', 'provider'=>($model ? $model->provider_id : null )] ).'</li>',
                            ],
                            'encodeLabels'=>false,
                            'options' => ['class' => 'dropdown-menu dropdown-menu-right']
                        ],
                        'encodeLabel' => false,
                        'options'=>[
                                'class'=>isset($class) ? $class : 'btn btn-warning',
                        ]
                        ]
                    );
                },
                'format' => 'raw'
            ],        
            
            [
                'header' => Yii::t('app','Account'),
                'format' => 'html',
                'value' => function($model){ return Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('app','Account'),
                        ['provider/current-account','id'=>$model->provider_id], ['class'=>'btn btn-default']); }
            ],

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>

</div>
