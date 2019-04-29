<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\helpers\UserA;
use yii\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */

$this->title = Yii::t('westnet', 'Vendor Liquidation Preview').': '.$model->vendor_liquidation_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'vendor_id',
                'value' => $model->vendor->fullName
            ],
            [
                'attribute' => 'periodMonth',
                'format' => 'raw',
                'value' => $model->period ? $model->periodMonth : Yii::$app->formatter->asDate($model->date, 'MM-yyyy')
            ],
            [
                'label' => Yii::t('app', 'Date'),
                'value' => Yii::$app->formatter->asDate('now')
            ],
            [
                'label' => Yii::t('app', 'Total'),
                'value' => Yii::$app->formatter->asCurrency($total)
            ]
        ],
    ]) ?>

    <h2><?= Yii::t('app', 'Items') ?></h2>
    
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
        ],
        'rowOptions' => function ($model, $key, $index, $grid){
            return ['data-customer' => $model->contractDetail->contract->customer_id];
        },
        'options' => [
            'id' => 'details'
        ]
    ]); ?>
    
</div>

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