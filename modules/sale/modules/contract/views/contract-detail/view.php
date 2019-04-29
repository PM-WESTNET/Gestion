<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ContractDetail */

$this->title = $model->contract_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-detail-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'contract_id' => $model->contract_id, 'product_id' => $model->product_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'contract_id' => $model->contract_id, 'product_id' => $model->product_id], [
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
            'contract_id',
            'product_id',
            'to_date',
            'status',
        ],
    ]) ?>

</div>
