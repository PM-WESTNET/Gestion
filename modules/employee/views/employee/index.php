<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\employee\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Employee'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-responsive'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'employee_id',
            'fullName',
            'document_number',
            [
                'attribute' => 'address_id',
                'value' => function($model) {
                    return $model->address->fullAddress;
                }
            ],
            [
                'label' => Yii::t('app','Bills'),
                'value'=> function ($model){
                    return yii\bootstrap\ButtonDropdown::widget([
                        'label' => '<span class="glyphicon glyphicon-list-alt"></span> '. Yii::t('app','Bills'),
                        'dropdown' => [
                            'items' => [
                                '<li>'.Html::a('<span class="glyphicon glyphicon-eye-open"></span> '. 'Ver',
                                    ['employee-bill/index','employee_id'=>$model->employee_id]).'</li>',
                                
                                '<li>'.Html::a('<span class="glyphicon glyphicon-plus"></span> '. 'Nuevo',
                                    ['employee-bill/create', 'employee'=>($model ? $model->employee_id : null )] ).'</li>',
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
                                    ['employee-payment/index','employee_id'=>$model->employee_id]).'</li>',
                                
                                '<li>'.Html::a('<span class="glyphicon glyphicon-plus"></span> '. 'Nuevo',
                                    ['employee-payment/create', 'employee'=>($model ? $model->employee_id : null )] ).'</li>',
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
                        ['employee/current-account','id'=>$model->employee_id], ['class'=>'btn btn-default']); }
            ],

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>

</div>
