<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\components\widgets\agenda\task\TaskBundle;
use \app\modules\agenda\AgendaModule;
use webvimark\modules\UserManagement\models\User;

TaskBundle::register($this);

$user = User::findOne(Yii::$app->user->id);

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?php if(!$model->isNewRecord) : ?>
        <div class="row margin-bottom-half">
            <div class="col-lg-12">
                <span class="font-bold">
                    <?= AgendaModule::t('app', 'Task creator'); ?>:
                </span>
                <span class="label label-primary margin-right-quarter">
                    <?= $model->creator->username; ?>
                </span>
                <span class="label label-info">
                    <?= AgendaModule::t('app', date('l', $model->datetime))?>, <?=date('d/m/Y H:i', $model->datetime); ?>
                </span>
            </div>
        </div>
    <?php endif; ?>
    
    <?= $form->field($model, 'name')->textInput([
        'maxlength' => 255,        
        'disabled' => ($model->isParent()) ? false : true,
        ]) ?>
    
    <div class="row">
        
        <div class="col-sm-6">
            <?= $form->field($model, 'task_type_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\TaskType::find()->all(), 'task_type_id', 'name'), [
                'encode' => false, 
                'separator' => '<br/>',
                'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                    'modelClass' => AgendaModule::t('app', 'Task type'),
                ]),
                'disabled' => ($model->isParent()) ? false : true,
                ]) ?>
        </div>
        
        <div class="col-sm-6">
            <?= $form->field($model, 'category_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\Category::find()->all(), 'category_id', 'name'), [
                'encode' => false, 
                'separator' => '<br/>',
                'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                    'modelClass' => AgendaModule::t('app', 'Category'),
                ]),
            ]); ?>
        </div>
        
    </div>

    <?= $form->field($model, 'description')->textarea([
        'rows' => 6,        
        'disabled' => ($model->isParent()) ? false : true,
        ]) ?>    

    <div class="row">
        
        <div class="col-sm-12">
            <?php if($model->isNewRecord) : ?>
                <?= $form->field($model, 'priority')->dropdownList(\app\modules\agenda\models\Task::getPriorities(), [
                    'encode' => false, 
                    'separator' => '<br/>',
                    'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                        'modelClass' => AgendaModule::t('app', 'Priority'),
                    ]),
                    'disabled' => ($model->isParent()) ? false : true,
                    ]) ?>
            <?php else : ?>
                <?= $form->field($model, 'priority')->dropdownList(\app\modules\agenda\models\Task::getPriorities(), [
                    'encode' => false, 
                    'separator' => '<br/>',
                    'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                        'modelClass' => AgendaModule::t('app', 'Priority'),
                    ]),
                    'disabled' => ($model->isParent()) ? false : true,
                    ]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'status_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\agenda\models\Status::find()->all(), 'status_id', 'name'), [
                'encode' => false, 
                'separator' => '<br/>',
                'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                    'modelClass' => AgendaModule::t('app', 'Status'),
                ]),
            ]) 
            ?>
        </div>
    </div>

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

    <!-- Asignacion de usuarios -->
    <?php if($model->isParent()) : ?>
        <div class="panel panel-default <?= (isset($model->taskType) && $model->taskType->slug == \app\modules\agenda\models\TaskType::TYPE_BY_USER) ? '' : 'disabled'; ?>" id="user-selection">

        <div class="panel-heading">
            <h3 class="panel-title font-bold">Asignar usuarios</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" style="margin-bottom: 0;">

                <?php
                $userModel = Yii::$app->modules['agenda']->params['user']['class'];
                $users = $userModel::find()
                        ->select(['username as value', 'username as label', 'id as id'])
                        ->where([
                            'status' => User::STATUS_ACTIVE
                        ])
                        ->asArray()
                        ->all();
                ?>

                <small class="text-muted">Comience a escribir el nombre de usuario para ver las opciones</small>
                <?=
                AutoComplete::widget([
                    'options' => [
                        'id' => 'user-selection-input',
                        'class' => 'form-control',
                    ],
                    'id' => 'users',
                    'clientOptions' => [
                        'source' => $users,
                        'autoFill' => true,
                        'minLength' => '1',
                        'select' => new JsExpression("function( event, ui ) {
                            Task.addUser(event, ui);
                         }")
                    ],
                ]);
                ?>

                <div id="alert-already-selected" class="alert alert-warning" style="margin-top: 15px; display: none;">
                    Usuario ya asignado.
                </div>

                <?= $form->field($model, 'assignAllUsers')->checkbox() ?>
                
                <?php $userGroups = \app\modules\agenda\models\UserGroup::find()->all(); ?>
                <?php if(!empty($userGroups)) : ?>
                    <div>
                        <?= $form->field($model, 'userGroups')->checkboxList(yii\helpers\ArrayHelper::map($userGroups, 'group_id', 'name'));?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

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
                            <span data-user="<?= $selectedUser->id; ?>" style="cursor: pointer" class="remove-user glyphicon glyphicon-remove"></span>
                        </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a class="label label-danger clickable" data-delete-users>
                Eliminar todos los usuarios
            </a>
        </div>
    </div>
    <?php endif; ?>
    <!-- end Asignacion de usuarios -->

    <!-- Creación de eventos -->
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
            
            <div class="form-group margin-top-half">
                <button data-event="create-event" data-event-type="<?= \app\modules\agenda\models\EventType::EVENT_NOTE_ADDED; ?>" data-event-user="<?= isset($user) && !empty($user) ? $user->username : '' ; ?>" type="button" class="btn btn-info">Crear nota</button>
            </div>
        </div>

        <div class="panel-footer">
            <?php if(count($model->events) > 0) : ?>
                <?php foreach ($model->events as $event) : ?>
                    <?php echo $this->render('/event/build_note', [
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
    <!-- end Creación de eventos -->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? AgendaModule::t('app', 'Create') : AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    #user-selection.disabled{
        
    }
</style>

<script>
    <?= $this->registerJs("Task.init();"); ?>
    <?= $this->registerJs("Task.setCreateEventInputUrl('". yii\helpers\Url::to(['/agenda/event/build-note'], true) ."');"); ?>
    <?= $this->registerJs("Task.setFindCategoryDefaultDurationUrl('". yii\helpers\Url::to(['/agenda/category/fetch-category'], true) ."');"); ?>
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
</script>