<?php

use app\modules\westnet\models\Node;
use app\modules\westnet\models\search\EmptyAdsSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel EmptyAdsSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('westnet','Empty Ads not used');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="empty-ads-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create Empty ADS'), 
        ['/westnet/ads/print-empty-ads'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'code',
            'payment_code',
            [
                'header' => Yii::t('app', 'Company'),
                'value' => function ($model){
                    return $model->company->name;
                }
            ],
            [
                'header' => Yii::t('westnet', 'Node'),
                'value' => function ($model){
                    return Node::findOne(['node_id' => $model->node_id])->name;
                }
            ],


            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=> '{print}',
                'buttons' => [
                    'print' => function($url, $model){
                        return '<a href="'. yii\helpers\Url::to(['/westnet/ads/print-one-empty-ads', 'empty_ads_id' => $model->empty_ads_id]).
                                '" target="_blank" class="btn btn-print"><span class="glyphicon glyphicon-print"></span></a>';
                    }
                ],
            ],
        ],
    ]); ?>

</div>
