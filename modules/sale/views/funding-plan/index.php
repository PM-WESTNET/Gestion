<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\FundingPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Funding Plan of').": ".$product->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => $product->name, 'url' => ['product/view', 'id' => $product->product_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Funding Plans');
?>
<div class="funding-plan-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [ 'modelClass' => Yii::t('app','Funding Plan'),]),
            ['create', 'id'=> $product->product_id],
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'qty_payments',
            'amount_payment:currency',
            [
                'label' => Yii::t('app', 'Taxes per fee'),
                'format' => 'currency',
                'value'=>function($model){
                    return $model->getFinalTaxesAmount();
                }
            ],
            [
                'label' => Yii::t('app', 'Fee amount with Taxes'),
                'format' => 'currency',
                'value'=>function($model){
                    return $model->getFinalAmount();
                }
            ],
            [
                'label' => Yii::t('app', 'Total amount funded'),
                'format' => 'currency',
                'value'=>function($model){
                    return $model->getFinalTotalAmount();
                }
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'value'=>function($model){
                    return Yii::t('app', ucfirst($model->status));
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>
</div>