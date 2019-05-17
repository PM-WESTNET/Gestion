<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Track */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tracks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="track-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->track_id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->getDeletable()) {
            echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->track_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
        }?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'track_id',
            'name',
            'slug',
            'description:ntext',
            'use_payment_card:boolean'
        ],
    ]) ?>

</div>
