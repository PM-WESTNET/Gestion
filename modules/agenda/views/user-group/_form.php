<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\components\widgets\agenda\task\TaskBundle;

TaskBundle::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\agenda\models\UserGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    
    <!-- Asignacion de usuarios -->
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
                            'status' => webvimark\modules\UserManagement\models\User::STATUS_ACTIVE
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
    <!-- end Asignacion de usuarios -->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \app\modules\agenda\AgendaModule::t('app', 'Create') : \app\modules\agenda\AgendaModule::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

<?= $this->registerJs("Task.init();"); ?>
<?php if (!empty($model->users)) : ?>
    <?php foreach ($model->users as $user) : ?>
            Task.setUser('<?= $user->id; ?>');
    <?php endforeach; ?>
<?php endif; ?>

</script>
