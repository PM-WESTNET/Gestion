<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('partner', 'Liquidations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Name'),
                'value' => function($model){
                    return $model['name'];
                },
            ],
            [
                'label' => Yii::t('app', 'Date'),
                'value' => function($model){
                    return Yii::$app->formatter->asDate($model['date']);
                },
            ],
            [
                'header' => Yii::t('partner','Partner'),
                'value' => function($model){
                    return $model['name'];
                }
            ],
            [
                'header' => Yii::t('accounting','Debit'),
                'value' => function($model){
                    return Yii::$app->formatter->asCurrency($model['debit']);
                }
            ],
            [
                'header' => Yii::t('accounting','Credit'),
                'value' => function($model){
                    return Yii::$app->formatter->asCurrency($model['credit']);
                }
            ],
            [
                'format' => 'html',
                'value' => function($model){
                    return Html::a('<span class="glyphicon glyphicon-show"></span> '.Yii::t('partner','Items'),
                        ['liquidation/liquidation-items','PartnerLiquidationSearch[partner_liquidation_id]'=>$model['partner_liquidation_id']], ['class'=>'btn btn-width btn-default']); }
            ]
        ],
    ]); ?>

</div>
