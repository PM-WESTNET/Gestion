<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\FundingPlan */

$this->title = Yii::t('app', 'Funding Plan of').": ".$model->product->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = ['label' => $model->product->name, 'url' => ['product/view', 'id' => $model->product_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Funding Plans'), 'url' => ['index', 'id' => $model->product_id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="funding-plan-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('app', 'Update'), ['update', 'id' => $model->funding_plan_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a('<span class="glyphicon glyphicon-remove"></span> '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->funding_plan_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'qty_payments',
            'amount_payment:currency',
            [
                'label' => Yii::t('app', 'Taxes per fee'),
                'format' => 'currency',
                'value'=> $model->getFinalTaxesAmount()
            ],
            [
                'label' => Yii::t('app', 'Fee amount with Taxes'),
                'format' => 'currency',
                'value'=> $model->getFinalAmount()
            ],
            [
                'label' => Yii::t('app', 'Total amount funded'),
                'format' => 'currency',
                'value' => $model->getFinalTotalAmount()
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'value' => Yii::t('app', ucfirst($model->status)),
            ],
        ]
    ]) ?>

</div>
