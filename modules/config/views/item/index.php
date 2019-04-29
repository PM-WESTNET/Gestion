<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\config\models\search\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Config Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

   <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . ConfigModule::t('config', 'Create {modelClass}', [
        'modelClass' => ConfigModule::t('config', 'Item'),
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

            'item_id',
            'attr',
            [
                'attribute' => 'category.name',
                'header' => Yii::t('app', 'Category')
            ],
            'type',
            'default',
            'label',
            // 'description',
            // 'multiple',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
