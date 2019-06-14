<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\agenda\AgendaModule;
use app\modules\agenda\models\TaskType;
use app\modules\agenda\models\Status;
use app\modules\agenda\models\Task;
use app\modules\agenda\models\Category;
use yii\jui\DatePicker;

?>
<?php $form = ActiveForm::begin([
    'id' => 'search-form',
    'method' => 'get',
    'action' => ['default/index']
]); ?>

    <div class="row">
        <div class="col-sm-4">
            <label class="display-block">
                <?= AgendaModule::t('app', 'Task creator'); ?>
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
                <?= AgendaModule::t('app', 'Assignations'); ?>
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
            <?= $form->field($model, 'task_type_id')->dropdownList(ArrayHelper::map(TaskType::find()->all(), 'task_type_id', 'name'), [
                'encode' => false,
                'separator' => '<br/>',
                'prompt' => Yii::t('app', 'Select'),
                ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'status_id')->dropdownList(ArrayHelper::map(Status::find()->all(), 'status_id', 'name'), [
                'encode' => false,
                'separator' => '<br/>',
                'prompt' => Yii::t('app', 'Select'),
            ]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'priority')->dropdownList(Task::getPriorities(), [
                'encode' => false,
                'separator' => '<br/>',
                'prompt' => Yii::t('app', 'Select'),
            ]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'category_id')->dropdownList(ArrayHelper::map(Category::find()->all(), 'category_id', 'name'), [
                'encode' => false,
                'separator' => '<br/>',
                'prompt' => Yii::t('app', 'Select'),
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'from_date')->widget(DatePicker::class, [
                    'language' => Yii::$app->language,
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>[
                        'class'=>'form-control dates',
                    ]
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'to_date')->widget(DatePicker::class, [
                'language' => Yii::$app->language,
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class'=>'form-control dates',
                ]
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 form-group">
            <?= Html::submitButton('Filtrar', ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
