<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\modules\ticket\models\Action;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Status;
use app\modules\agenda\models\Task;
use app\modules\agenda\models\TaskType;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\modules\ticket\components\schemas\SchemaDefault;
use kartik\depdrop\DepDrop;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Status */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="status-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'is_open')->checkbox() ?>

    <?= $form->field($model, 'generate_action')->checkbox(['id' => 'generate-action-checkbox']) ?>

    <!--Datos para el modelo status_has_action (configuraciones para la creacion de tickets o tareas)-->
    <div  id="action-form-field" class="hidden">
        <?= $form->field($status_has_action, 'action_id')->widget(Select2::class, [
            'data' => Action::getForSelect(),
            'options' => ['placeholder' => Yii::t('app','Select ...')],
        ])?>
    </div>

    <div id="text1-form-field" class="hidden">
        <?= $form->field($status_has_action, 'text_1')->textInput() ?>
    </div>

    <div id="text2-form-field" class="hidden">
        <?= $form->field($status_has_action, 'text_2')->textarea() ?>
    </div>

    <div class="row">

        <div id="ticket-fields" class="hidden">
            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'ticket_category_id')->widget(Select2::class, [
                    'data' => ArrayHelper::map(Category::getForSelect(), 'category_id', 'name'),
                     'options' => [
                            'placeholder' => Yii::t("app", "Select"),
                            'encode' => false,
                            'id' => 'ticket_category_id',
                            'autoclose' => true
                    ]
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'ticket_status_id')->widget(DepDrop::class, [
                'options' => [
                        'id' =>'ticket_status_id'
                ],
                'data' => SchemaDefault::getStatusesForSelect(),
                'type' => DepDrop::TYPE_SELECT2,
                'pluginOptions'=>[
                    'depends' => ['ticket_category_id'],
                    'initDepends' => ['ticket_category_id'],
                    'initialize' => true,
                    'url' => Url::to(['category/get-status-from-schema'])
                ]
                ]) ?>
            </div>
        </div>

        <div id="task-fields" class="hidden">
            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'task_category_id')->widget(Select2::class, [
                    'data' => \app\modules\agenda\models\Category::getForSelect()
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'task_priority')->widget(Select2::class, [
                    'data' => Task::getPriorities(),
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'task_time')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showMeridian' => false,
                    ]
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'task_type_id')->widget(Select2::class, [
                    'data' => TaskType::getForSelect()
                ])?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($status_has_action, 'task_status_id')->widget(Select2::class, [
                    'data' => \app\modules\agenda\models\Status::getForSelect()
                ])?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    var StatusForm = new function(){
        this.init= function(){
            StatusForm.showHideActionField();

            $('#generate-action-checkbox').on('click', function (evt) {
                StatusForm.showHideActionField();
            })

            if($('#statushasaction-action_id').val()) {
                this.showHideFields($('#statushasaction-action_id').val());
            }

            $('#statushasaction-action_id').on('change', function () {
                StatusForm.showHideFields($('#statushasaction-action_id').val());

            });
        }

        this.showHideActionField = function () {
            if($('#generate-action-checkbox').prop('checked')) {
                $('#action-form-field').removeClass('hidden');
            } else {
                $('#action-form-field').addClass('hidden');
            }
        }

        this.showHideFields = function (category_id) {
            $.ajax({
                url: "<?= Url::to(['action/get-type'])?>",
                data: {action_id: category_id},
                dataType: 'json',
                method: 'GET'
            }).done(function (response) {
                if(response.status == 'success') {
                    if(response.type == '<?= Action::TYPE_TICKET ?>') {
                        $('#text1-form-field').removeClass('hidden');
                        $('#text2-form-field').removeClass('hidden');
                        $('#ticket-fields').removeClass('hidden');
                        $('#task-fields').addClass('hidden');
                    }

                    if(response.type == '<?= Action::TYPE_EVENT ?>' ) {
                        $('#text1-form-field').removeClass('hidden');
                        $('#text2-form-field').addClass('hidden');
                        $('#ticket-fields').addClass('hidden');
                        $('#task-fields').removeClass('hidden');
                    }
                }
            })
        }

    }
</script>
<?php $this->registerJs('StatusForm.init()')?>
