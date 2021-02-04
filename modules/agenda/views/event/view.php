<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Event */

$this->title = $model->event_id;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Events'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(\app\modules\agenda\AgendaModule::t('app', 'Update'), ['update', 'id' => $model->event_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(\app\modules\agenda\AgendaModule::t('app', 'Delete'), ['delete', 'id' => $model->event_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => \app\modules\agenda\AgendaModule::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'event_id',
            'task_id',
            'user_id',
            'event_type_id',
            'date',
            'time',
            'datetime',
        ],
    ]) ?>

</div>
