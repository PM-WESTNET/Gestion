<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\ProductSearch $searchModel
 */

$this->title = Yii::t('app', 'Plan price history');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('app', 'Go to plans'), ['plan/index'], ['class' => 'btn btn-info']) ?>
            <?php
            //Link a datos con grafico
            $link_url = ['product-price/graph'];
            if($product = Yii::$app->request->get('id'))
                $link_url['product_id'] = $product;
            echo Html::a(Yii::t('app', 'Data with chart'), $link_url, ['class' => 'btn btn-default']) ?>
        </p>
    </div>
    
    <h3><?= Html::encode($model->name) ?> <small>#<?= $model->code; ?></small></h3>
    
    <hr>
    
    <?php \yii\widgets\Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            ['class' => yii\grid\CheckboxColumn::className()],
            //'product_id',
            [
                'attribute'=>'date',
                'value'=>function($model, $key, $index, $column){ return Yii::$app->formatter->asDate($model->date,'medium'); }
            ],
            'time',
            [
                'label'=>Yii::t('app','Net Price'),
                'attribute'=>'net_price',
                'format'=>['currency']
            ],
            [
                'label'=>Yii::t('app','Final Price'),
                'attribute'=>'finalPrice',
                'format'=>['currency']
            ],
            [
                'attribute'=>'exp_date',
                'format'=>['date']
            ],
//            'description:ntext',
            // 'status',
            // 'balance',
            // 'create_timestamp:datetime',
            // 'update_timestamp:datetime',
            // 'unit_id',
            // 'class',

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{delete}',
                'urlCreator' => function( $action, $model, $key, $index ){
                    return \yii\helpers\Url::toRoute(["product-price/$action", 'id'=>$model->product_price_id]);
                }
            ],
        ],
    ]); ?>
    
    <?php \yii\widgets\Pjax::end(); ?>

    
</div>
