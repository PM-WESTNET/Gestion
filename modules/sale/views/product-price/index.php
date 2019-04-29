<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\ProductPriceSearch $searchModel
 */

$this->title = Yii::t('app', 'Product Prices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-price-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
      'modelClass' => Yii::t('app','Product price'),
    ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'product_price_id',
            'net_price',
            'taxes',
            'date:date',
            'time',
            // 'timestamp:datetime',
            // 'exp_timestamp:datetime',
            // 'exp_date',
            // 'exp_time',
            // 'update_timestamp:datetime',
            // 'status',
            // 'product_id',

            ['class' => 'app\components\grid\ActionColumn'],
        ],
    ]); ?>

</div>
