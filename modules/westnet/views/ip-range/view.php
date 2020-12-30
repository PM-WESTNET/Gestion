<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\IpRank */

$this->title = $model->ip_range_id;
$this->params['breadcrumbs'][] = ['label' => 'Ip Ranks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ip-rank-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ip_range_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a('Delete', ['delete', 'id' => $model->ip_range_id], [
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
            'ip_range_id',
            [
                'attribute' => 'ip_start',
                'value' => function($model) {
                    return long2ip($model->ip_start);
                }
            ],
            [
                'attribute' => 'ip_end',
                'value' => function($model) {
                    return long2ip($model->ip_end);
                }
            ],
            'status',
            'node_id',
        ],
    ]) ?>


    <h3><?= Yii::t('app', 'Subnets')?></h3>

    <?=
        GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->getSubnets()]),
            'columns' => [
                'ip_range_id',
                [
                    'attribute' => 'ip_start',
                    'value' => function($model) {
                        return long2ip($model->ip_start);
                    }
                ],
                [
                    'attribute' => 'ip_end',
                    'value' => function($model) {
                        return long2ip($model->ip_end);
                    }
                ],
                'status',
            ]
        ])
    
    ?>
</div>
