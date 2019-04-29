<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Color */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ticket Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="color-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->color_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->color_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'color_id',
            'color',
            'order',
            'name',
            'slug',
        ],
    ]) ?>

</div>
