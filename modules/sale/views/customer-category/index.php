<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\sale\models\CustomerCategory;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\CustomerCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-category-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('app', 'Customer Category'),
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
                'attribute'=>'parent_id',
                'value'=>function($model){return $model->parent ? $model->parent->name : '';},
                'filter'=>  yii\helpers\ArrayHelper::map(CustomerCategory::find()->all(), 'customer_category_id', 'name'),
                'header'=>Yii::t('app','Parent')
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
