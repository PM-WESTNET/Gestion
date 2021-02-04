<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\helpers\UserA;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\westnet\models\search\VendorLiquidationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('westnet', 'Vendor Liquidations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-index">

    <div class="title">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= UserA::a('Calcular comisiones', ['batch'], ['class' => 'btn btn-primary']); ?>
        
        <?= UserA::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
            'modelClass' => Yii::t('westnet','Vendor Liquidation'),
        ]), 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'vendor_id',
                'value' => function($model){ return $model->vendor->fullName; },
                'filter' => \kartik\widgets\Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'vendor_id',
                    'options' => ['placeholder' => Yii::t('app', 'Search')],
                    'data' => \app\modules\westnet\models\Vendor::findForSelect(),
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ])
            ],
            [
                'attribute' => 'period',
                'format' => 'raw',
                'value' => function($model){ return $model->period ? $model->periodMonth : Yii::$app->formatter->asDate($model->date, 'MM-yyyy'); },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel, 
                    'attribute' => 'period', 
                    'dateFormat' => Yii::$app->formatter->dateFormat,
                    'options' => [
                        'class' => 'form-control'
                    ]
                ])
            ],
            [
                'attribute' => 'total',
                'format' => 'currency',
                'value' => function($model){ return $model->total ? $model->total : 0.0; }
            ],
            [
                'attribute' => 'status',
                'value' => function($model){ return Yii::t('app', ucfirst($model->status)); },
                'filter' => ['draft' => Yii::t('app', 'Draft'), 'payed' => Yii::t('app', 'Payed')]
            ],

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>

</div>
