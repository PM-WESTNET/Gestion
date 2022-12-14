<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Status */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="status-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->status_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->status_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php $columns = [
        'status_id',
        'name',
        'description:ntext',
        'is_open:boolean',
        'generate_action:boolean'
    ];

    if($model->generate_action) {
        $columns [] =
            [
                'label' => Yii::t('app','Action'),
                'value' => function ($model) {
                    return $model->actionConfig? $model->actionConfig->action->name : '';
                }
            ]
        ;
    }?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $columns
    ]) ?>

</div>
