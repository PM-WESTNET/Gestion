<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerLog */

$this->title = $model->customer_log_id;
$this->params['breadcrumbs'][] = ['label' => 'Customer Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->customer_log_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a('Delete', ['delete', 'id' => $model->customer_log_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'customer_log_id',
            'action',
            'before_value',
            'new_value',
            'date',
            'customer_customer_id',
            'user_id',
            'observations',
        ],
    ]) ?>

</div>
