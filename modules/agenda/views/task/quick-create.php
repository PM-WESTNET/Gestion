<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;    
use app\components\widgets\agenda\task\TaskBundle;
use webvimark\modules\UserManagement\models\User;
use app\modules\agenda\AgendaModule;
use app\modules\agenda\models\TaskType;
use app\modules\agenda\models\Category;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use kartik\time\TimePicker;
use app\modules\agenda\models\Task;
use app\modules\agenda\models\Status;
use app\modules\agenda\models\UserGroup;
use yii\helpers\Url;
use app\modules\agenda\models\EventType;

TaskBundle::register($this);

$user = User::findOne(Yii::$app->user->id);

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\Task */

$this->title = AgendaModule::t('app', 'Update {modelClass}: ', [
            'modelClass' => 'Task',
        ]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => AgendaModule::t('app', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->task_id]];
$this->params['breadcrumbs'][] = AgendaModule::t('app', 'Update');
?>
<div class="task-update container-fluid">
    
    <?php if(!empty($message)) : ?>
    <div class="alert alert-success">
        
        <?= $message; ?>
        
    </div>
    <?php endif; ?>
    
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

        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'name')->textInput([
                    'maxlength' => 255,
                    'disabled' => ($model->isParent()) ? false : true,
                ])?>
            </div>
            
        </div>
        <div class="row">
            <div class="col-sm-12">
            <?= $form->field($model, 'description')->textarea([
                'rows' => 2,
                'disabled' => ($model->isParent()) ? false : true,
            ])?>
            </div>
        </div>
        <div class="row">
                        
            <div class="col-xs-6">
                <?=
                $form->field($model, 'task_type_id')->dropdownList(ArrayHelper::map(TaskType::find()->all(), 'task_type_id', 'name'), [
                    'encode' => false,
                    'separator' => '<br/>',
                    'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                        'modelClass' => AgendaModule::t('app', 'Task type'),
                    ]),
                ])
                ?>
            </div>

            <div class="col-xs-6">
                <?= $form->field($model, 'category_id')->dropdownList(ArrayHelper::map(Category::find()->all(), 'category_id', 'name'), [
                    'encode' => false,
                    'separator' => '<br/>',
                    'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                        'modelClass' => AgendaModule::t('app', 'Category'),
                    ]),
                ]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-4">
                <?= $form->field($model, 'date')->widget(DatePicker::class, [
                    'language' => 'es-AR', 
                    'dateFormat' => 
                    'dd-MM-yyyy', 
                    'options' => [
                        'class' => 'form-control'
                        ]
                    ]) ?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'time')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showMeridian' => false,
                        'minuteStep' => 15
                    ]
                ]); ?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'duration')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'defaultTime' => '02:00:00',
                        'showMeridian' => false,
                        'minuteStep' => 15
                    ]
                ]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?=
                $form->field($model, 'priority')->dropdownList(Task::getPriorities(), [
                    'encode' => false,
                    'separator' => '<br/>',
                    'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                        'modelClass' => AgendaModule::t('app', 'Priority'),
                    ]),
                ])
                ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'status_id')->dropdownList(ArrayHelper::map(Status::find()->all(), 'status_id', 'name'), [
                    'encode' => false,
                    'separator' => '<br/>',
                    'prompt' => AgendaModule::t('app', 'Select {modelClass}', [
                        'modelClass' => AgendaModule::t('app', 'Status'),
                    ]),
                    ]) ?>
            </div>
        </div>

        <!-- Asignacion de usuarios -->
        <?php if($model->isParent()) : ?>
            <div class="panel panel-default <?= (isset($model->taskType) && $model->taskType->slug == TaskType::TYPE_BY_USER) ? '' : 'disabled'; ?>" id="user-selection">

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
                    <?= AutoComplete::widget([
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
                    ]) ?>
                    <div id="alert-already-selected" class="alert alert-warning" style="margin-top: 15px; display: none;">
                        Usuario ya asignado.
                    </div>

                    <?= $form->field($model, 'assignAllUsers')->checkbox() ?>

                    <?php $userGroups = UserGroup::find()->all(); ?>
                    <?php if(!empty($userGroups)) : ?>
                        <div>
                            <?= $form->field($model, 'userGroups')->checkboxList(ArrayHelper::map($userGroups, 'group_id', 'name'));?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="panel-footer">
                <label>Usuarios asignados</label>
                <a class="label label-danger clickable pull-right" data-delete-users>
                    Eliminar todos los usuarios
                </a>

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

                <div class="form-group">
                    <button data-event="create-event" data-event-type="<?= EventType::EVENT_NOTE_ADDED; ?>" data-event-user="<?= isset($user) && !empty($user) ? $user->username : ''; ?>" type="button" class="btn btn-info btn-xs">Crear nota</button>
                </div>
            </div>

            <?php if (count($model->events) > 0) : ?>
                <div class="panel-footer">
                <?php foreach ($model->events as $event) : ?>
                    <?= $this->render('/event/build_note', [
                        'old' => true,
                        'username' => $event->user->username,
                        'body' => $event->body,
                        'time' => $event->datetime,
                    ])?>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        <!-- end Creación de eventos -->

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? AgendaModule::t('app', 'Create') : AgendaModule::t('app', 'Update'), ['onclick'=> 'parent.closeModal();', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

<?php ActiveForm::end(); ?>

    </div>

</div>

<style>
    #user-selection.disabled{

    }
</style>

<script>
    
    <?= $this->registerJs("Task.init();"); ?>
    <?= $this->registerJs("Task.setCreateEventInputUrl('" . Url::to(['/agenda/event/build-note'], true) . "');"); ?>
    <?= $this->registerJs("Task.setFindCategoryDefaultDurationUrl('". Url::to(['/agenda/category/fetch-category'], true) ."');"); ?>
    <?= $this->registerJs("Task.setGetUserByUsernameUrl('". Url::to(['/agenda/user-group/get-user-by-username'], true) ."');"); ?>

    <?=
    $this->registerJs("Task.disableUserInput(parseInt(" . TaskType::findOne([
                'slug' => TaskType::TYPE_GLOBAL
            ])->task_type_id . "))");
    ?>
        
    <?php if (!empty($model->users)) : ?>
        <?php foreach ($model->users as $user) : ?>
            Task.setUser('<?= $user->id; ?>');
        <?php endforeach; ?>
    <?php endif; ?>
        
</script>