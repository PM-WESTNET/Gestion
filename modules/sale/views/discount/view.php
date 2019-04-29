<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Discount */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->discount_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->discount_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'discount_id',
            'name',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => Yii::t('app', ucfirst($model->status)),
            ],
            [
                'label' => Yii::t('app', 'Type'),
                'value' => Yii::t('app', ucfirst($model->type)),
            ],
            [
                'label' => Yii::t('app', 'Referenced'),
                'value' => Yii::t('app', $model->referenced ? 'Yes' : 'No' ),
            ],
            'value',
            'from_date',
            'to_date',
            'periods',
            [
                'label' => Yii::t('app', 'Apply to'),
                'value' => Yii::t('app', ucfirst($model->apply_to)),
            ],
            [
                'label' => Yii::t('app', 'Value from'),
                'value' => Yii::t('app', ucfirst($model->value_from)),
            ],
            [
                'label' => Yii::t('app', 'Producto'),
                'value' => ($model->value_from==\app\modules\sale\models\Discount::VALUE_FROM_PRODUCT ? $model->product->name : Yii::t('app', 'No apply')),
            ],

        ],
    ]) ?>

</div>
