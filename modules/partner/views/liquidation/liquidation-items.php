<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Liquidations'), 'url' => ['/partner/liquidation/list-liquidation']];
$this->title = Yii::t('partner', 'Liquidation Items - {name}', ['name'=>$model->partnerDistributionModelHasPartner->partnerDistributionModel->name]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

    </div>
    <?php

    echo \yii\bootstrap\Collapse::widget([
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                'content' => $this->render('_search', ['model' => $searchModel]),
                'encode' => false,
            ],
        ],
        'options' => [
            'class' => 'hidden-print'
        ]
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => Yii::t('partner','Type'),
                'value' => function($model){
                    return $model['type'];
                }
            ],
            [
                'label' => Yii::t('app', 'Date'),
                'value' => function($model){
                    return Yii::$app->formatter->asDate($model['date']);
                },
            ],
            [
                'header' => Yii::t('app','Description'),
                'value' => function($model){
                    return $model['description'];
                }
            ],
            [
                'header' => Yii::t('app','Amount'),
                'value' => function($model){
                    return Yii::$app->formatter->asCurrency($model['amount']);
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view}',
                'buttons'=>[
                    'view' => function ($url, $model, $key) {
                        if($model['type']=='Pago') {
                            return '<a href="'.Url::toRoute(['/provider/provider-payment/view', 'id'=>$model['model_id']]).'" class="btn btn-view" target="_blank"><span class="glyphicon glyphicon-eye-open"></span></a>';
                        } else if($model['type']=='Cobro') {
                            return '<a href="'.Url::toRoute(['/checkout/payment/view', 'id'=>$model['model_id']]).'" class="btn btn-view" target="_blank"><span class="glyphicon glyphicon-eye-open"></span></a>';
                        } else{
                            return '<a href="'.Url::toRoute(['/accounting/account-movement/view', 'id'=>$model['model_id']]).'" class="btn btn-view" target="_blank"><span class="glyphicon glyphicon-eye-open"></span></a>';
                        }

                    }
                ]
            ]
        ],
    ]); ?>

</div>
