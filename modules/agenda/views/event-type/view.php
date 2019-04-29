<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\EventType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Event types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-type-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(\app\modules\agenda\AgendaModule::t('app', 'Update'), ['update', 'id' => $model->event_type_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(\app\modules\agenda\AgendaModule::t('app', 'Delete'), ['delete', 'id' => $model->event_type_id], [
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
            'name',
            'description:ntext',
            'slug',
        ],
    ]) ?>

</div>
