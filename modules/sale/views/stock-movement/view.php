<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\StockMovement $model
 */

$this->title = $model->product->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock movements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-movement-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>    

    <?php 
    $values = [
        'in' => '<span style="color: green;" class="glyphicon glyphicon-arrow-up"></span> '. Yii::t('app','In'),
        'out' => '<span style="color: red;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','Out'),
        'r_in' => '<span style="color: gold;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','R. In'),
        'r_out' => '<span style="color: orange;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','R. Out')
    ];
                    
    ?>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'stock_movement_id',
            [
                'attribute' => 'company',
                'value' => $model->company->name,
            ],
            [
                'attribute' => 'type',
                'value' => $values[$model->type],
                'format' => ['html']
            ],
            'qtyAndUnit',
            [
                'attribute' => 'secondaryQtyAndUnit',
                'visible'=>app\modules\config\models\Config::getValue('enable_secondary_stock')
            ],
            'concept',
            [
                'attribute'=>'date',
                'format'=>['date','dd-MM-Y']
            ],
            'time',
            [
                'label' => Yii::t('app','Bill'),
                'value' => $model->bill_detail_id ? Html::a(Yii::t('yii','View'), ['bill/view', 'id'=>$model->billDetail->bill_id]) : '',
                'format' => 'html'
            ]
        ],
    ]) ?>

</div>
