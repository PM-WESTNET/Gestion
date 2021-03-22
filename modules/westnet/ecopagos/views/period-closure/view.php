<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\PeriodClosure */

$this->title = $model->period_closure_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Period Closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="period-closure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->period_closure_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->period_closure_id], [
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
            'period_closure_id',
            'datetime:datetime',
            'cashier_id',
            'payment_count',
            'first_payout_number',
            'last_payout_number',
            'date',
            'time',
            'date_from',
            'date_to',
            'status',
        ],
    ]) ?>

</div>
