<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\widgets\agenda\AgendaBundle;

$this->title = \app\modules\agenda\AgendaModule::t('app', 'My agenda');
AgendaBundle::register($this);
?>

<div class="container-fluid agenda">

    <div class="container">

        <div class="title">
            <h1><?= \app\modules\agenda\AgendaModule::t('app', 'My agenda'); ?></h1>            
        </div>

        
        <!-- Filters -->
        <?php $form = ActiveForm::begin([
            'id' => 'search-form',
            'method' => 'get',
            'action' => array('default/index')
        ]); ?>
        
            <div class="row">
                <div class="col-sm-4">
                    <label class="display-block">
                        <?= \app\modules\agenda\AgendaModule::t('app', 'Task creator'); ?>
                    </label>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default font-size-xs <?= ($model->create_option == 'all') ? 'active' : '' ;?>">
                            <input <?= ($model->create_option == 'all') ? 'checked' : '' ;?> type="radio" name="TaskSearch[create_option]" value="all" id="created_by_all"> Todos
                        </label>
                        <label class="btn btn-default font-size-xs <?= ($model->create_option == 'me') ? 'active' : '' ;?>">
                            <input <?= ($model->create_option == 'me') ? 'checked' : '' ;?> type="radio" name="TaskSearch[create_option]" value="me" id="created_by_me"> Creadas por mi
                        </label>
                        <label class="btn btn-default font-size-xs <?= ($model->create_option == 'others') ? 'active' : '' ;?>">
                            <input <?= ($model->create_option == 'others') ? 'checked' : '' ;?> type="radio" name="TaskSearch[create_option]" value="others" id="created_by_others"> Creadas por otros
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label class="display-block">
                        <?= \app\modules\agenda\AgendaModule::t('app', 'Assignations'); ?>
                    </label>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default font-size-xs <?= ($model->user_option == 'all') ? 'active' : '' ;?>">
                            <input <?= ($model->user_option == 'all') ? 'checked' : '' ;?> type="radio" name="TaskSearch[user_option]" value="all" id="user_id"> Todos
                        </label>
                        <label class="btn btn-default font-size-xs <?= ($model->user_option == 'me') ? 'active' : '' ;?>">
                            <input <?= ($model->user_option == 'me') ? 'checked' : '' ;?> type="radio" name="TaskSearch[user_option]" value="me" id="user_id"> Asignadas a mi
                        </label>
                        <label class="btn btn-default font-size-xs <?= ($model->user_option == 'others') ? 'active' : '' ;?>">
                            <input <?= ($model->user_option == 'others') ? 'checked' : '' ;?> type="radio" name="TaskSearch[user_option]" value="others" id="user_id"> Asignadas a otros
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'task_type_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\TaskType::find()->all(), 'task_type_id', 'name'), [
                        'encode' => false, 
                        'separator' => '<br/>', 
                        'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Any {modelClass}', [
                            'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Task Type'),
                        ]),
                        ]) ?>
                </div>
            </div>
        
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'status_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\Status::find()->all(), 'status_id', 'name'), [
                        'encode' => false, 
                        'separator' => '<br/>', 
                        'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Any {modelClass}', [
                            'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Status'),
                        ]),
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'priority')->dropdownList(\app\modules\agenda\models\Task::getPriorities(), [
                        'encode' => false, 
                        'separator' => '<br/>', 
                        'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Any {modelClass}', [
                            'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Priority'),
                        ]),
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'category_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\Category::find()->all(), 'category_id', 'name'), [
                        'encode' => false, 
                        'separator' => '<br/>', 
                        'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Any {modelClass}', [
                            'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Category'),
                        ]),
                    ]); ?>
                </div>
            </div>
        
            <div class="row">
                <div class="col-lg-12 form-group">
                    <?= Html::submitButton('Filtrar', ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        
        <?php ActiveForm::end(); ?>
        <!-- end Filters -->

    </div>

    <div id="agenda" class="padding-full">
        <?php
        //Eventos
        echo \yii2fullcalendar\yii2fullcalendar::widget(array(
            'events' => $events,
            'options' => [
                'lang' => 'es',
            ],
            'eventAfterAllRender' => "function(view) { "
            . "Agenda.init();"
            . "}",
        ));
        ?>
    </div>
    
    <!-- References -->
    <div class="container margin-bottom-full well well-sm hidden-print">
        
        <div class="row">
            <div class="col-sm-12 text-center">
                <h3>Referencias</h3>
            </div>
        </div>  
        
        <div class="row">
            <div class="col-lg-12 text-center">
                <h4><?= \app\modules\agenda\AgendaModule::t('app', 'Task statuses'); ?></h4>
                <span class="task task-completed display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Completed task'); ?></span>
                <span class="task task-in_progress display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'In progress task'); ?></span>
                <span class="task task-pending display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Pending task'); ?></span>
                <span class="task task-stopped display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Stopped task'); ?></span>
                <span class="task task-created display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Created task'); ?></span>
            </div>
        </div>     
        <div class="row margin-top-full">
            <div class="col-lg-12 text-center">
                <h4><?= \app\modules\agenda\AgendaModule::t('app', 'Task priorities'); ?></h4>
                <span class="task task-in_progress priority-1 display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Low priority'); ?></span>
                <span class="task task-in_progress priority-2 display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Medium priority'); ?></span>
                <span class="task task-in_progress priority-3 display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'High priority'); ?></span>
                <span class="task task-in_progress priority-4 display-inline-block margin-top-quarter"><?= \app\modules\agenda\AgendaModule::t('app', 'Highest priority'); ?></span>
            </div>
        </div>     
        
    </div>
    <!-- end References -->

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
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>

<?php
    $this->registerJs("Agenda.constructor(
        new Date(),
        '.task',
        '#task-modal',
        '#task-iframe',
        '".\yii\helpers\Url::to(['/agenda/task/quick-update'], true)."',
        '".\yii\helpers\Url::to(['/agenda/default/update-agenda'], true)."',
        '".\yii\helpers\Url::to(['/agenda/task/quick-create'], true)."'
    );");
?>