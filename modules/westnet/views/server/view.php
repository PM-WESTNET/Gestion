<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Server */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="server-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->server_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->server_id], [
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
            'name',
            [
                'label' => Yii::t('app', 'Status'),
                'value'=> Yii::t('app', ucfirst($model->status))
            ],
            'url',
            'load_balancer_type',
            [
                'attribute' => 'ip_of_load_balancer',
                'value' => function ($model){
                    return long2ip($model->ip_of_load_balancer);
                }
            ],
            'token',
            'user',
            'class'
        ],
    ]) ?>

</div>
