<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\ProductPrice $model
 */

$this->title = $model->product_price_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-price-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->product_price_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->product_price_id], [
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
            'product_price_id',
            'net_price',
            'taxes',
            'date',
            'time',
            'timestamp:datetime',
            'exp_timestamp:datetime',
            'exp_date',
            'exp_time',
            'update_timestamp:datetime',
            'status',
            'product_id',
        ],
    ]) ?>

</div>
