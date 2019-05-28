<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\widgets\agenda\AgendaBundle;
use app\modules\agenda\AgendaModule;
use yii2fullcalendar\yii2fullcalendar;
use yii\helpers\Url;
use yii\bootstrap\Collapse;

$this->title = AgendaModule::t('app', 'My agenda');
AgendaBundle::register($this);

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid agenda">

    <div class="container">

        <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= Collapse::widget([
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters'),
                'content' => $this->render('_filters', ['model' => $model]),
                'encode' => false,
            ],
        ],
        'options' => [
            'class' => 'print',
            'aria-expanded' => 'false'
        ]
    ])?>

    <div class="row well well-sm">
        <div class="col-sm-2">
            <h6>Referencias</h6>
        </div>
        <div class="col-sm-5">
                <h6><?= AgendaModule::t('app', 'Task statuses'); ?> : </h6>
            <span class="label label-default"><?= AgendaModule::t('app', 'Created task'); ?></span>
            <span class="label label-info"><?= AgendaModule::t('app', 'In progress task'); ?></span>
            <span class="label label-warning"><?= AgendaModule::t('app', 'Pending task'); ?></span>
            <span class="label label-danger"><?= AgendaModule::t('app', 'Stopped task'); ?></span>
            <span class="label label-success"><?= AgendaModule::t('app', 'Completed task'); ?></span>
            </div>
        <div class="col-sm-5">
               <h6><?= AgendaModule::t('app', 'Task priorities'); ?> : </h6>
               <span class="label label-default"> * <?= AgendaModule::t('app', 'Low priority'); ?></span>
               <span class="label label-default"> ** <?= AgendaModule::t('app', 'Medium priority'); ?></span>
               <span class="label label-default"> *** <?= AgendaModule::t('app', 'High priority'); ?></span>
               <span class="label label-default"> **** <?= AgendaModule::t('app', 'Highest priority'); ?></span>
        </div>
    </div>

    <div id="agenda" class="padding-full">
        <?= yii2fullcalendar::widget([
            'events' => $events,
            'options' => [
                'lang' => 'es',
            ],
            'eventAfterAllRender' => "function(view) { "
            . "Agenda.init();"
            . "}",
        ]);
        ?>
    </div>

    <div class="modal fade" id="task-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Tarea</h4>
                </div>
                <div class="modal-body">
                    <iframe id="task-iframe" src=""  style="overflow-x: hidden;">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
    $this->registerJs("Agenda.constructor(
        new Date(),
        '.task',
        '#task-modal',
        '#task-iframe',
        '".Url::to(['/agenda/task/quick-update'], true)."',
        '".Url::to(['/agenda/default/update-agenda'], true)."',
        '".Url::to(['/agenda/task/quick-create'], true)."'
    );");
?>