<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\IpRank */

$this->title = $model->ip_rank_id;
$this->params['breadcrumbs'][] = ['label' => 'Ip Ranks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ip-rank-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ip_rank_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a('Delete', ['delete', 'id' => $model->ip_rank_id], [
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
            'ip_rank_id',
            'ip_start',
            'ip_end',
            'status',
            'node_id',
        ],
    ]) ?>

</div>
