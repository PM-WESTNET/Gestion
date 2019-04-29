<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\pagomiscuentas\models\PagomiscuentasLiquidation */

$this->title = $model->pagomiscuentas_liquidation_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pagomiscuentas Liquidations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pagomiscuentas-liquidation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->pagomiscuentas_liquidation_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->pagomiscuentas_liquidation_id], [
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
            'pagomiscuentas_liquidation_id',
            'file',
            'created_at',
            'updated_at',
            'number',
            'account_movement_id',
        ],
    ]) ?>

</div>
