<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\grid\SerialColumn;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AccessPoint */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Access Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-point-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->access_point_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->access_point_id], [
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
            'access_point_id',
            'name',
            'status',
            'strategy_class',
            'node_id',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Ip Ranges')?></h3>

    <?=
        GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->getIpRanges()]),
            'options' => [ 'id' => 'range_table'],
            'columns' => [
                ['class' => SerialColumn::class],

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
                [
                    'attribute' => 'last_ip',
                    'value' => function($model) {
                        if ($model->last_ip) {
                            return long2ip($model->last_ip);
                        }
                    }
                ],
            ]
        ])
    
    ?>

</div>
