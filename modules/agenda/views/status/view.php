<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\agenda\AgendaModule;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Status */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Task Statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="status-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . AgendaModule::t('app', 'Update'), ['update', 'id' => $model->status_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable)
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . AgendaModule::t('app', 'Delete'), ['delete', 'id' => $model->status_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => AgendaModule::t('app', 'Are you sure you want to delete this item?'),
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
            'color',
            'slug',
        ],
    ]) ?>

</div>
