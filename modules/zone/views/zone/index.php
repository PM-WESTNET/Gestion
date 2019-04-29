<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\zone\models\Zone;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\zone\models\search\ZoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Zones');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zone-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Zone')]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'name',
                'value'=>function($model, $key, $index, $column){
        
                    $searchModel = $column->grid->filterModel;
                    if($searchModel->isFiltered()){
                        return $model->name;
                    }
        
                    if(empty($model->parent_id)){
                        return "<strong>$model->name</strong>";
                    }else{
                        return $model->tabName; 
                    }
                },
                'format'=>'html'
            ],
            [
                'attribute'=>'type',
                'filter'=>Yii::$app->params['type_zone'],
                'value'=>function($model){return Yii::t('app',  ucfirst($model->type)); },
                'header'=>Yii::t('app','Type'),
            ],                           
            [
                'attribute'=>'parent_id',
                'value'=>function($model){return $model->parent ? $model->parent->name : '';},
                'filter'=>  yii\helpers\ArrayHelper::map(Zone::find()->all(), 'zone_id', 'name'),
                'header'=>Yii::t('app','Parent')
            ],       
            [
                'attribute'=>'postal_code',
                'value'=>function($model){return $model->postal_code ? $model->postal_code : '';},
            ],                           
            [
                'attribute'=>'status',
                'filter'=>[
                    'enabled'=>Yii::t('app','Enabled'),
                    'disabled'=>Yii::t('app','Disabled'),
                ],
                'value'=>function($model){return Yii::t('app',  ucfirst($model->status)); }
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
            
        ],
    ]); ?>

</div>
