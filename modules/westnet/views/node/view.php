<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Node */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet','Nodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->node_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->node_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'node_id',
            [
                'label'=>Yii::t('westnet', 'Server'),
                'attribute'=> 'server.name',
            ],
            [
                'label'=>Yii::t('westnet', 'Parent Node'),
                'value'=> ($model->parentNode ? $model->parentNode->name : '' ),
            ],
            'name',
            [
                'attribute'=>'zone_id',
                'label'=>Yii::t('app', 'Zone'),
                'value'=>$model->zone ? $model->zone->name : null,
            ],
            [
                'attribute' => 'geocode',
                'value' => function($model){
                    return $model->geocode? $model->geocode : Yii::t('westnet', 'Not set yet');
                }
            ],
            [
                'attribute'=>'status',
                'value'=>Yii::t('westnet', ucfirst($model->status)),
            ],
            'subnet',
            [
                'label'=>Yii::t('westnet', 'Ip Start'),
                'value'=>$model->ipRange ? $model->ipRange->getIpStartFormatted() : null,
            ],
            [
                'label'=>Yii::t('westnet', 'Ip End'),
                'value'=>$model->ipRange ? $model->ipRange->getIpEndFormatted() : null,
            ],
            [
                'attribute'=>'has_ecopago_close',
                'value'=>Yii::t('app', ($model->has_ecopago_close  ? 'Yes' : 'No' )),
            ],
        ],
    ]) ?>

    <h2><?php echo Yii::t('westnet', 'Ecopagos')?></h2>

    <?=    GridView::widget([
        'dataProvider' =>new ActiveDataProvider([
            'query' => $model->getEcopagos(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',

        ],
    ]);
    ?>

</div>
