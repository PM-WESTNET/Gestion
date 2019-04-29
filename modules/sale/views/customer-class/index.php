<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\CustomerClassSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer Classes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-class-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('app', 'Customer Class'),
    ]), 
            ['create'], 
            ['class' => 'btn btn-success'])
            ;?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'customer_class_id',
            'name',
            'code_ext',
            [
                'attribute'=>'is_invoiced',
                'filter'=>[
                    '1'=>Yii::t('app','Yes'),
                    '0'=>Yii::t('app','No'),
                ],
                'value'=>function($model){
                    if($model->is_invoiced){
                        return Yii::t('app','Yes'); 
                        
                    }
                    else{
                        return Yii::t('app','No');
                    }
                }
            ],
            //'is_invoiced:boolean',
            'tolerance_days',
            'percentage_tolerance_debt',
            [
                'attribute'=>'colour',
                'content' => function($model, $key, $index, $column){
                    $content = "<div style='float:left; margin-rigth: 10px; width: 40px; height: 20px; background-color: {$model->colour}'> </div>";
                    return $content;
                },
                 'filter'=>false,        
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function($model){
                    return Yii::t('app', ucfirst($model->status));
                }
            ],            
            //'colour',
            // 'percentage_bill',
            // 'days_duration',

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
