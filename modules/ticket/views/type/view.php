<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\ticket\TicketModule;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Type */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->type_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->type_id], [
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
            'type_id',
            'user_group_id',
            'name',
            'description:ntext',
            'slug',
        ],
    ]) ?>

</div>
