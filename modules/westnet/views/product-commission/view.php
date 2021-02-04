<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\ProductCommission */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Product Commissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-commission-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->product_commission_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->product_commission_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'product_commission_id',
            'name',
            'percentage',
            'value',
        ],
    ]) ?>
    
</div>
