<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\zone\models\Zone */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zone-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->zone_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->zone_id], [
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
            'zone_id',
            'name',
            [
                'attribute'=>'type',
                'value'=>Yii::t('app',  ucfirst($model->type)),
            ],
            'create_timestamp:datetime',
            'update_timestamp:datetime',
            [
                'attribute'=>'parent_id',
                'value'=>$model->parent ? $model->parent->name : '',
            ],       
            [
                'attribute'=>'status',
                'value'=>Yii::t('app',  ucfirst($model->status))
            ],
            'postal_code',
        ],
    ]) ?>

</div>
