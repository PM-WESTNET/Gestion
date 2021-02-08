<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;    
use app\components\widgets\agenda\task\TaskBundle;

TaskBundle::register($this);

$user = webvimark\modules\UserManagement\models\User::findOne(Yii::$app->user->id);

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Task */

$this->title = \app\modules\agenda\AgendaModule::t('app', 'Update {modelClass}: ', [
            'modelClass' => 'Task',
        ]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \app\modules\agenda\AgendaModule::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->task_id]];
$this->params['breadcrumbs'][] = \app\modules\agenda\AgendaModule::t('app', 'Update');
?>

<!-- Tabs -->
<ul class="nav nav-tabs" id="task-tabs" role="tablist">
    
    <li role="presentation" class="active">
        <a href="#task-home" aria-controls="task-home" role="tab" data-toggle="tab">Información de la tarea</a>
    </li>
    
    <?php if($model->taskType->slug == app\modules\agenda\models\TaskType::TYPE_BY_USER && !empty($model->children)) : ?>
        <li role="presentation">
            <a href="#task-assigned" aria-controls="task-assigned" role="tab" data-toggle="tab">Detalle de asignaciones</a>
        </li>
    <?php endif; ?>
        
    <?php if ($model->isAssignedUser(Yii::$app->user->id) && $model->isPostponable()) : ?>
        <li role="presentation">
            <a href="#task-postpone" aria-controls="task-postpone" role="tab" data-toggle="tab"><?php echo \app\modules\agenda\AgendaModule::t('app', 'Postpone task'); ?></a>
        </li>
    <?php endif; ?>
        
</ul>
<!-- end Tabs-->

<div class="tab-content">
    
    <!-- Tab Task info -->
    <div role="tabpanel" class="tab-pane fade in active padding-top-half" id="task-home">
        
        <div class="task-update container">

            <?php if(!empty($message)) : ?>
            
                <div class="alert alert-success">
                    <?= $message; ?>
                </div>
            
            <?php endif; ?>

            <div class="task-form">

                <?php $form = ActiveForm::begin(); ?>

                <?php if(!$model->isNewRecord) : ?>
                
                    <!-- Task Creator and Asignee -->
                    <div class="row margin-bottom-half">
                        <div class="col-lg-12">
                            
                            <span class="font-bold">
                                <?= \app\modules\agenda\AgendaModule::t('app', 'Task information'); ?>: 
                            </span>
                            
                            <span class="label label-primary margin-right-quarter">
                                <?= \app\modules\agenda\AgendaModule::t('app', 'Task creator'); ?>:  <?= $model->creator->username; ?>
                            </span>
                            
                            <span class="label label-info margin-right-quarter">
                                <?= \app\modules\agenda\AgendaModule::t('app', date('l', $model->datetime))?>, <?=date('d/m/Y H:i', $model->datetime); ?>
                            </span>                            
                    
                            <?php if($model->isChild() && !empty($model->users)) : ?>

                                <!-- Task assignee -->
                                <?php $users = $model->users; ?>

                                <span class="label label-default margin-right-quarter">
                                    <?= \app\modules\agenda\AgendaModule::t('app', 'Task asignee'); ?>: 
                                    <?= end($users)->username; ?>
                                </span>
                                <!-- end Task assignee -->

                            <?php endif; ?>
                            
                        </div>                        
                    </div>
                    <!-- end Task Creator and Asignee -->
                
                <?php endif; ?>

                <!-- Task name -->
                <div class="row">
                    
                    <div class="col-sm-12">
                        <?= $form->field($model, 'name')->textInput([
                            'maxlength' => 255,
                            'disabled' => ($model->isParent()) ? false : true,
                        ]) ?>        
                    </div>
                    
                </div>
                <!-- end Task name -->
                    
                <div class="row">                    
                    
                    <div class="col-sm-6">
                        <?= $form->field($model, 'task_type_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\TaskType::find()->all(), 'task_type_id', 'name'), [
                            'encode' => false,
                            'separator' => '<br/>',
                            'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Select {modelClass}', [
                                'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Task type'),
                            ]),
                            'disabled' => true,
                        ]) ?>
                    </div>

                    <div class="col-sm-6">
                        <?= $form->field($model, 'category_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\Category::find()->all(), 'category_id', 'name'), [
                            'encode' => false, 
                            'separator' => '<br/>', 
                            'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Select {modelClass}', [
                                'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Category'),
                            ]),
                        ]); ?>
                    </div>
                    
                </div>

                <?= $form->field($model, 'description')->textarea([
                    'rows' => 2,
                    'disabled' => ($model->isParent()) ? false : true,
                ]) ?>

                <!-- Datetime and duration -->
                <div class="row">
                    
                    <div class="col-sm-4">
                        <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                            'language' => 'es-AR', 
                            'dateFormat' => 
                            'dd-MM-yyyy', 
                            'options' => [
                                'class' => 'form-control'
                                ]
                        ]) ?>
                    </div>
                    
                    <div class="col-sm-4">
                        <?= $form->field($model, 'time')->widget(\kartik\time\TimePicker::classname(), [
                            'pluginOptions' => [
                                'showMeridian' => false,
                                'minuteStep' => 15
                            ]
                        ]); ?>
                    </div>
                    
                    <div class="col-sm-4">
                        <?= $form->field($model, 'duration')->widget(\kartik\time\TimePicker::classname(), [
                            'pluginOptions' => [
                                'defaultTime' => '02:00:00',
                                'showMeridian' => false,
                                'minuteStep' => 15
                            ]
                        ]); ?>
                    </div>
                    
                </div>
                <!-- end Datetime and duration -->

                <div class="row">
                    
                    <div class="col-sm-6">
                        <?= $form->field($model, 'priority')->dropdownList(\app\modules\agenda\models\Task::getPriorities(), [
                            'encode' => false,
                            'separator' => '<br/>',
                            'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Select {modelClass}', [
                                'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Priority'),
                            ]),
                            'disabled' => true,
                        ]) ?>
                    </div>
                    
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\Status::find()->all(), 'status_id', 'name'), [
                            'encode' => false, 
                            'separator' => '<br/>', 
                            'prompt' => \app\modules\agenda\AgendaModule::t('app', 'Select {modelClass}', [
                                'modelClass' => \app\modules\agenda\AgendaModule::t('app', 'Status'),
                            ]),
                            ]) ?>
                    </div>                    
                    
                </div>

                <!-- Users assignations -->
                <?php if ($model->isParent()) : ?>
                
                    <div class="panel panel-default <?= (isset($model->taskType) && $model->taskType->slug == \app\modules\agenda\models\TaskType::TYPE_BY_USER) ? '' : 'disabled'; ?>" id="user-selection">

                        <div class="panel-footer">
                            
                            <label>Usuarios asignados</label>

                            <?php if (empty($model->users)) : ?>
                            
                                <small id="no-users" class="text-muted">No hay usuarios asignados</small>
                                <?php endif; ?>

                            <input type="hidden" name="Task[users][]"/>
                            
                            <div id="user-list">
                                
                                <?php if (!empty($model->users)) : ?>
                                
                                    <?php foreach ($model->users as $selectedUser) : ?>
                                
                                        <span id="container-user-<?= $selectedUser->id; ?>" class="label label-default" style="margin: 15px;">
                                            <label for="user-<?= $selectedUser->id; ?>"><?= $selectedUser->username; ?></label>
                                            <input type="hidden" name="Task[users][]" value="<?= $selectedUser->id; ?>" id="user-<?= $selectedUser->id; ?>"/>
                                        </span>
                                
                                    <?php endforeach; ?>
                                
                                <?php endif; ?>
                                
                            </div>
                            
                        </div>
                        
                    </div>
                
                <?php endif; ?>
                <!-- end Users assignations -->

                <!-- Events -->
                <div class="panel panel-default" id="event-list">

                    <div class="panel-heading">
                        <h3 class="panel-title font-bold">Eventos y notas</h3>
                    </div>

                    <div class="panel-body">
                        
                        <label>Notas</label>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <textarea placeholder="Contenido de la nota" data-event="event-body" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-group margin-top-half no-margin-bottom">
                            <button data-event="create-event" data-event-type="<?= \app\modules\agenda\models\EventType::EVENT_NOTE_ADDED; ?>" data-event-user="<?= isset($user) && !empty($user) ? $user->username : ''; ?>" type="button" class="btn btn-info">Crear nota</button>
                        </div>
                        
                    </div>

                    <div class="panel-footer">
                        
                        <?php if (count($model->events) > 0) : ?>
                        
                            <?php foreach ($model->events as $event) : ?>
                        
                                <?= $this->render('/event/build_note', [
                                    'old' => true,
                                    'username' => $event->user->username,
                                    'body' => $event->body,
                                    'time' => $event->datetime,
                                ]); ?>
                        
                            <?php endforeach; ?>
                        
                        <?php else: ?>
                            <small id="no-events" class="text-muted">No hay eventos anteriores</small>  
                        <?php endif; ?>
                            
                    </div>

                </div>
                <!-- end Events -->

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['onclick'=> 'parent.closeModal();', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

        </div>
        
    </div>
    <!-- end Tab Task info -->
    
    <?php if($model->taskType->slug == app\modules\agenda\models\TaskType::TYPE_BY_USER && !empty($model->children)) : ?>
        <!-- Tab task assignations -->
        <div role="tabpanel" class="tab-pane fade padding-quarter" id="task-assigned">

            <div class="container">

                <label><?= \app\modules\agenda\AgendaModule::t('app', 'Work detailed by assigned user'); ?></label>

                <?php foreach ($model->children as $childTask) : ?>

                    <div class="task task-<?= $childTask->status->slug; ?> margin-bottom-quarter">

                        <span class="label label-default margin-right-quarter">
                            <?= date('d/m/Y', strtotime($childTask->date)); ?> <?= date('H:i', strtotime($childTask->time)); ?>
                        </span>

                        <span class="label label-default margin-right-quarter">
                            <?= $childTask->status->name; ?>
                        </span>

                        <?php if(!empty($childTask->users)) : $users = $childTask->users; ?>
                            <span><?= end($users)->username; ?></span>
                        <?php endif;?>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>
        <!-- end Tab task assignations -->
    <?php endif; ?>
        
    <?php if ($model->isAssignedUser(Yii::$app->user->id) && $model->isPostponable()) : ?>
        <!-- Postpone tasks tab -->    
        <div role="tabpanel" class="tab-pane fade padding-top-half" id="task-postpone">

            <div class="container">

                <!-- Task Creator and Asignee -->
                <div class="row margin-bottom-half">
                    <div class="col-lg-12">

                        <span class="font-bold">
                            <?= \app\modules\agenda\AgendaModule::t('app', 'Postpone task'); ?> [<?= $model->name; ?>]: 
                        </span>

                        <span class="label label-primary margin-right-quarter">
                            <?= \app\modules\agenda\AgendaModule::t('app', 'Task creator'); ?>:  <?= $model->creator->username; ?>
                        </span>

                        <span class="label label-info margin-right-quarter">
                            <?= \app\modules\agenda\AgendaModule::t('app', date('l', $model->datetime))?>, <?=date('d/m/Y H:i', $model->datetime); ?>
                        </span>                            

                        <?php if($model->isChild() && !empty($model->users)) : ?>

                            <!-- Task assignee -->
                            <?php $users = $model->users; ?>

                            <span class="label label-default margin-right-quarter">
                                <?= \app\modules\agenda\AgendaModule::t('app', 'Task asignee'); ?>: 
                                <?= end($users)->username; ?>
                            </span>
                            <!-- end Task assignee -->

                        <?php endif; ?>

                    </div>                        
                </div>
                <!-- end Task Creator and Asignee -->

                <!-- Postpone form -->
                <div class="row">                

                    <?php $postponeForm = ActiveForm::begin([
                        'action' => \yii\helpers\Url::to(['/agenda/task/postpone-task', 'id' => $model->task_id], true),
                        'method' => 'post'
                    ]); ?>                

                    <div class="col-sm-12">
                        <?= $postponeForm->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                            'language' => 'es-AR', 
                            'dateFormat' => 'dd-MM-yyyy', 
                            'options' => [
                                'class' => 'form-control'
                                ]
                        ]); ?>
                    </div>
                    
                    <div class="col-sm-12">

                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['onclick'=> 'parent.closeModal();', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                        
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
                <!-- end Postpone form -->

            </div>

        </div>
        <!-- end Postpone tasks tab -->
    <?php endif; ?>
    
</div>

<script>

    <?= $this->registerJs("Task.init();"); ?>
    <?= $this->registerJs("Task.setCreateEventInputUrl('" . yii\helpers\Url::to(['/agenda/event/build-note'], true) . "');"); ?>
    <?= $this->registerJs("Task.setFindCategoryDefaultDurationUrl('". yii\helpers\Url::to(['/agenda/category/fetch-category'], true) ."');"); ?>
    <?= $this->registerJs("Task.setGetUserByUsernameUrl('". yii\helpers\Url::to(['/agenda/user-group/get-user-by-username'], true) ."');"); ?>
        
    <?=
    $this->registerJs("Task.disableUserInput(parseInt(" . \app\modules\agenda\models\TaskType::findOne([
                'slug' => \app\modules\agenda\models\TaskType::TYPE_GLOBAL
            ])->task_type_id . "))");
    ?>
    <?php if (!empty($model->users)) : ?>
        <?php foreach ($model->users as $user) : ?>
            Task.setUser('<?= $user->id; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
        
    <?= $this->registerJs("$('#task-tabs a').first().tab('show')"); ?>
    
</script>