<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\helpers\UserA;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Product $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p style="width: 80%;">
            <?= UserA::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('app', 'Update'), ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) ?>

            <?= UserA::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'New Stock Movement'), ['stock-movement/create', 'product_id' => $model->product_id], ['class' => 'btn btn-success']) ?>


            <?= UserA::a('<span class="glyphicon glyphicon-dashboard"></span> '.Yii::t('app', 'Stock History'), ['stock-movement/index', 'product_id' => $model->product_id], ['class' => 'btn btn-info']) ?>
            <?php if(Yii::$app->params['funding_plan']){
                echo UserA::a('<span class="glyphicon glyphicon-usd"></span> '.Yii::t('app', 'Funding Plans'), ['funding-plan/index', 'id' => $model->product_id], ['class' => 'btn btn-info']) ;
            }
            ?>
            <?= UserA::a('<span class="glyphicon glyphicon-print"></span> '.Yii::t('app', 'Print barcodes'), ['product/print-barcodes','id'=>$model->product_id], ['class' => 'btn btn-warning']) ?>
            <?php if($model->deletable) echo UserA::a('<span class="glyphicon glyphicon-remove"></span> '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->product_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>

        </p>
    </div>
    
    <?php
    //Para mostrar IVA
    $taxRates = '';
    foreach($model->taxRates as $rate){
        $taxRates .= $rate->tax->name.': '.$rate->name .'<br>';
    }
    ?>

    <?php
    $attributes = [
        'product_id',
        [
            'attribute' => 'company.name',
            'label' => Yii::t('app', 'Company'),
            'value' => ($model->company_id ? $model->company->name : Yii::t('app', 'All') ),
        ],
        'name',
        'system',
        'netPrice:currency',
        [
            'attribute' => 'taxRates',
            'label' => Yii::t('app', 'Taxes'),
            'value' => $taxRates,
            'format' => 'html'
        ],
        'finalPrice:currency',
        'code',
        'description:ntext',
        [
            'attribute'=>'status',
            'value'=>Yii::t('app',  ucfirst($model->status))
        ],
        'balance',
        [
            'attribute'=>'unit_id',
            'value'=>$model->unit->name
        ],
//        'secondary_balance',
//        [
//            'attribute'=>'secondary_unit_id',
//            'value'=>$model->secondaryUnit ? $model->secondaryUnit->name : null
//        ],
        'create_timestamp:date',
        'update_timestamp:date',
        [
            'attribute'=>'unit_id',
            'value'=>$model->unit->name
        ],
    ];

    if (Yii::$app->getModule('accounting')) {
        $attributes[] = [
            'label' => Yii::t('accounting', 'Account'),
            'value' => ($model->account ? $model->account->name : "" )
        ];
    }
    
    if (Yii::$app->getModule('westnet')) {
        $attributes[] = [
            'label' => Yii::t('westnet', 'Commission'),
            'value' => ($model->commission ? $model->commission->name : "" )
        ];
    }

    $attributes[] = [
        'label'=>$model->getAttributeLabel('barcode'),
        'format'=>'image',
        'value'=>  \yii\helpers\Url::toRoute(['product/barcode','id'=>$model->product_id])
    ];

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]);

    foreach($model->media as $media){
        echo app\modules\media\components\view\Preview::widget(['media' => $media]);
    }
    ?>

</div>
