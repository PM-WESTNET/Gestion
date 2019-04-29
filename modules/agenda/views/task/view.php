<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\agenda\AgendaModule;

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Task */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => AgendaModule::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . AgendaModule::t('app', 'Update'), ['update', 'id' => $model->task_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable)
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . AgendaModule::t('app', 'Delete'), ['delete', 'id' => $model->task_id], [
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
            [
                'label'=>$model->getAttributeLabel('Creator'),
                'value'=>$model->creator ? $model->creator->username : ''
            ],
            [
                'label'=>$model->getAttributeLabel('taskType'),
                'value'=>$model->taskType ? $model->taskType->name : ''
            ],
            [
                'label'=>$model->getAttributeLabel('status'),
                'value'=>$model->status ? $model->status->name : ''
            ],
            'name',
            'description:ntext',
            [
                'label'=>$model->getAttributeLabel('date'),
                'value'=> date('d/m/Y', strtotime($model->date)),
            ],
            [
                'label'=>$model->getAttributeLabel('time'),
                'value'=> date('H:i', strtotime($model->time)),
            ],
            [
                'label'=>$model->getAttributeLabel('priority'),
                'value'=> $model->getPriorities()[$model->priority]
            ],
        ],
    ]) ?>    
    
    <?php if($model->taskType->slug == app\modules\agenda\models\TaskType::TYPE_BY_USER && !empty($model->children)) : ?>
    
        <label><?= \app\modules\agenda\AgendaModule::t('app', 'Work detailed by assigned user'); ?></label>
    
        <?php foreach ($model->children as $childTask) : ?>

            <a href="<?= \yii\helpers\Url::to(['/agenda/task/update', 'id' => $childTask->task_id], true); ?>">
                <div class="task task-<?= $childTask->status->slug; ?> margin-bottom-quarter">
                    <span class="label label-default margin-right-quarter">
                        <?= date('d/m/Y', strtotime($childTask->date)); ?> <?= date('H:i', strtotime($childTask->time)); ?>
                    </span>
                    <span class="label label-default margin-right-quarter">
                        <?= $childTask->status->name; ?>
                    </span>
                    <?php $users = $childTask->users; ?>
                    <span><?= end($users)->username; ?></span>
                </div>
            </a>
    
        <?php endforeach; ?>
    
    <?php endif; ?>

</div>
