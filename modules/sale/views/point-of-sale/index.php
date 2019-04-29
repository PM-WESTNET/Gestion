<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\PointOfSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Points of Sale');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-of-sale-index">

    <?php 
    //Mensaje
    if(!Yii::$app->params['companies']['enabled'])
        echo \yii\bootstrap\Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
          'body' => Yii::t('app', 'Only visible to superadmin.'),
     ]); ?>
    
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => Yii::t('app','Point of Sale'),
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
                'class' => 'app\components\companies\CompanyColumn'
            ],
            'name',
            'number',
            [
                'attribute'=>'status',
                'filter'=>[
                    'enabled'=>Yii::t('app','Enabled'),
                    'disabled'=>Yii::t('app','Disabled'),
                ],
                'value'=>function($model){return Yii::t('app',  ucfirst($model->status)); }
            ],
            [
                'attribute' => 'default',
                'filter' => [0 => Yii::t('yii', 'No'), 1 => Yii::t('yii', 'Yes')],
                'format' => ['boolean']
            ],
            'electronic_billing:boolean',
            'description',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
