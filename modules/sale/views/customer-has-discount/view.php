<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */

$this->title = Yii::t('app', 'Discounts applied to {customer}', ['customer' => $model->customer->name]) . " - " . $model->discount->name;

$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id'=> $model->customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discounts applied'), 'url' => ['/sale/customer-has-discount/index', 'customer_id'=> $model->customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;
?>
<div class="customer-has-discount-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->cutomer_has_discount_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->cutomer_has_discount_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'discount.name',
            'from_date',
            'to_date',
            'description',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => Yii::t('app', ucfirst($model->status))
            ],

        ],
    ]) ?>

</div>
