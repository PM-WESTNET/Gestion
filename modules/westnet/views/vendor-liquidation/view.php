<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\helpers\UserA;
use yii\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */

$this->title = Yii::t('westnet', 'Vendor Liquidation').': '.$model->vendor_liquidation_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        <?= UserA::a(
                '<span class="glyphicon glyphicon-plus"></span> '.
                Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('westnet', 'Manual Liquidation Item')]),
                ['vendor-liquidation-item/create', 'liquidation_id' => $model->vendor_liquidation_id],
                ['class' => 'btn btn-success']) ?>
        <?= UserA::a(Yii::t('app', 'Update'), ['update', 'id' => $model->vendor_liquidation_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo UserA::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->vendor_liquidation_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php
        if($model->status == \app\modules\westnet\models\VendorLiquidation::VENDOR_LIQUIDATION_DRAFT) {
            echo Html::a("<span class='glyphicon glyphicon-list-alt'></span> " . Yii::t('app', 'Create Bill'), [
                'create-bill',
                'id' => $model->vendor_liquidation_id], [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('westnet', 'Are you sure you want to create the Bill?'),
                        'method' => 'post',
                    ]
                ]
            );
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'vendor_liquidation_id',
            [
                'attribute' => 'vendor_id',
                'value' => $model->vendor->fullName
            ],
            [
                'attribute' => 'periodMonth',
                'format' => 'raw',
                'value' => $model->period ? $model->periodMonth : Yii::$app->formatter->asDate($model->date, 'MM-yyyy')
            ],
            'date',
            [
                'attribute' => 'status',
                'value' => Yii::t('app', ucfirst($model->status))
            ],
            [
                'attribute' => 'total',
                'format' => 'currency',
            ]
        ],
    ]) ?>

    <h2><?= Yii::t('app', 'Items') ?></h2>
    
    <?php
    $customers = [];
    foreach($itemsDataProvider->getModels() as $item){
        if($item->contractDetail){
            $customers[$item->contractDetail->contract->customer_id] = $item->contractDetail->contract->customer->fullName;
        }
    }
    ?>
    
    <div class="hidden-print">
        <label><?= Yii::t('app', 'Search Customer') ?></label>
        <?php echo Select2::widget([
            'name' => 'customer_id',
            'data' => $customers,
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'id' => 'customer_id'],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $itemsDataProvider,
        'filterModel' => false,
        'columns' => [
            'description',
            [
                'label' => Yii::t('app','Customer'),
                'value' => function($model){
                    return $model->contractDetail ? UserA::a($model->contractDetail->contract->customer->fullName,['/sale/customer/view', 'id' => $model->contractDetail->contract->customer->customer_id]) : NULL;
                },
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app','Customer Number'),
                'value' => function($model){
                    return $model->contractDetail ? UserA::a($model->contractDetail->contract->customer->code,['/sale/customer/view', 'id' => $model->contractDetail->contract->customer->customer_id]) : NULL;
                },
                'format' => 'html'
            ],
            [
                'label' => Yii::t('app','Contract'),
                'value' => function($model){
                    return $model->contractDetail ? UserA::a($model->contractDetail->contract_id, ['/sale/contract/contract/view', 'id' => $model->contractDetail->contract_id]) : NULL;
                },
                'format' => 'html'
            ],
            'amount:currency',
            [
                'class' => 'app\components\grid\ActionColumn',
                'controller' => 'vendor-liquidation-item',
                'template' => '{view} {cancel} {delete}',
                'buttons' => [
                    'cancel' => function ($url, $model, $key) {
                        return UserA::a('<span class="glyphicon glyphicon-remove"></span>', $url, ['class' => 'btn btn-warning']);
                    }
                ]
            ],
        ],
        'rowOptions' => function ($model, $key, $index, $grid){
            return [
                'data-customer' => $model->contractDetail ? $model->contractDetail->contract->customer_id : ''
            ];
        },
        'options' => [
            'id' => 'details'
        ]
    ]); ?>
    
    <h3><?= Yii::t('app', 'Summary') ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'plansCount',
                'format' => 'integer',
                'label' => 'Cantidad de planes'
            ],
            [
                'attribute' => 'addCount',
                'format' => 'integer',
                'label' => 'Cantidad de adicionales'
            ],
            [
                'attribute' => 'discountCount',
                'format' => 'integer',
                'label' => 'Cantidad de descuentos aplicados'
            ],
            [
                'attribute' => 'manualCount',
                'format' => 'integer',
                'label' => 'Otros items'
            ],
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'format' => 'integer',
                'value' => $itemsDataProvider->totalCount,
                'label' => 'Cantidad total de items'
            ],
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'plansTotal',
                'format' => 'currency',
                'label' => 'Total de planes'
            ],
            [
                'attribute' => 'addTotal',
                'format' => 'currency',
            ],
            [
                'attribute' => 'discountTotal',
                'format' => 'currency',
            ],
            [
                'attribute' => 'manualTotal',
                'format' => 'currency',
                'label' => 'Total otros items'
            ],
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'total',
                'format' => 'currency',
            ],
        ],
    ]) ?>
    
</div>

<dir class="row visible-print">
    <div class="col-sm-12 text-center" style="margin-top: 1cm;">
        Firma vendedor
    </div>
</dir>

<script>

var liquidation = new function(){
    this.init = function(){
        $('#customer_id').on('change', function(){
            $('#details tr').css('background-color', '');
            
            var customerId = $(this).val();
            $row = $("[data-customer="+customerId+"]");
            
            $('#details tbody').prepend($row);
            $row.css('background-color', 'gold');
        })
    }
}

</script>
<?php $this->registerJs('liquidation.init();') ?>