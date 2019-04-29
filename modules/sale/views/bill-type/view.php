<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\BillType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bill Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bill-type-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->bill_type_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->bill_type_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <?php
    //BillTypes que puede generar este BillType
    $types = null;
    foreach($model->billTypes as $i => $billType){
        $types .= Html::a(($i == 0) ? $billType->name: ", $billType->name", ['bill-type/view', 'id' => $billType->bill_type_id]);
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bill_type_id',
            'name',
            'code',
            'customer_required:boolean',
            'multiplier:boolean',
            'multiplier',
            [
                'attribute' => 'view',
                'value' => Yii::t('app', ucfirst($model->view))
            ],
            'class',
            [
                'attribute' => 'invoice_class_id',
                'value' => $model->invoiceClass ? $model->invoiceClass->name : null
            ],
            [
                'attribute' => 'billTypes',
                'value' => $types,
                'format' => 'html'
            ],
            'applies_to_buy_book:boolean',
            'applies_to_sale_book:boolean',
        ],
    ]) ?>

</div>
