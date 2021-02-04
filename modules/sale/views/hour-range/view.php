<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\HourRange */

$this->title = $model->hour_range_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Hour Ranges'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hour-range-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->hour_range_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->hour_range_id], [
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
            'hour_range_id',
            'from',
            'to',
        ],
    ]) ?>

</div>
