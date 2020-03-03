<?php

use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\components\widgets\agenda\ticket\TicketBundle;
use webvimark\modules\UserManagement\models\User;
use app\modules\ticket\models\Category;
use app\modules\ticket\TicketModule;
use app\modules\agenda\models\UserGroup;
use yii\helpers\ArrayHelper;
use app\modules\ticket\models\Status;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\ticket\components\schemas\SchemaDefault;

$user = User::findOne(Yii::$app->user->id);
TicketBundle::register($this);
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(['id' => 'ticket-form']); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id']) ?>

    <?php

    $category_query = Category::find();

    if (isset(Yii::$app->params['tickets_categories_showed']) && !empty(Yii::$app->params['tickets_categories_showed'])) {
        $category_query->andWhere(['IN', 'category_id', Yii::$app->params['tickets_categories_showed']]);
    }

    $category_query->orderBy(['name' => SORT_ASC ]);

    $categories = $category_query->all();
    $aCategories = [];
    $aOptions = [];
    foreach($categories as $category) {
        $aCategories[$category->category_id] = $category->name;
        $aOptions[$category->category_id] = ['data-notify'=> $category->external_user_id ? $category->external_user_id : "0"];
    }
    echo $form->field($model, 'category_id')->widget(Select2::class,[
        'data' => $aCategories,
        'options' => [
            'placeholder' => Yii::t("app", "Select"),
            'encode' => false,
            'options' => $aOptions,
            'id' => 'category_id'
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'autoclose' => true,
        ]
    ]);
    ?>

    <div class="form-group" id="div-mesa-user">
        <label class="control-label"><?= TicketModule::t('app', 'Mesa User'); ?></label>
        <div id="mesa-user"></div>
    </div>
    <!-- Asignacion de usuarios -->
    <div class="panel panel-default" id="user-selection">

        <div class="panel-heading">
            <h3 class="panel-title font-bold"><?= TicketModule::t('app', 'Assign users to this ticket'); ?></h3>
        </div>

        <div class="panel-body">
            <div class="form-group" style="margin-bottom: 0;">

                <?php
                $userModel = Yii::$app->modules['ticket']->params['user']['class'];
                $users = $userModel::find()
                        ->select(['username as value', 'username as label', 'id as id'])
                        ->where([
                            'status' => User::STATUS_ACTIVE
                        ])
                        ->asArray()
                        ->all();
                ?>

                <small class="text-muted"><?= TicketModule::t('app', 'Start writting an username to see more options'); ?></small>
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
                            Ticket.addUser(event, ui);
                         }")
                    ],
                ]);
                ?>

                <div id="alert-already-selected" class="alert alert-warning" style="margin-top: 15px; display: none;">
                    <?= TicketModule::t('app', 'User already assigned'); ?>
                </div>

                <?php $userGroups = UserGroup::find()->all(); ?>
                <?php if (!empty($userGroups)) : ?>
                    <div>
                        <?= $form->field($model, 'userGroups')->checkboxList(ArrayHelper::map($userGroups, 'group_id', 'name')); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="panel-footer">
            <label><?= TicketModule::t('app', 'Assigned users'); ?></label>
            <a class="label label-danger clickable pull-right" data-delete-users>
                Eliminar todos los usuarios
            </a>
            <input type="hidden" name="Ticket[users][]"/>

            <div id="user-list">
                <?php if (!empty($model->users)) : ?>
                    <?php foreach ($model->users as $selectedUser) : ?>
                        <span id="container-user-<?= $selectedUser->id; ?>" class="label label-default" style="margin: 15px;">
                            <label for="user-<?= $selectedUser->id; ?>"><?= $selectedUser->username; ?></label>
                            <input type="hidden" name="Ticket[users][]" value="<?= $selectedUser->id; ?>" id="user-<?= $selectedUser->id; ?>"/>
                            <span data-user="<?= $selectedUser->id; ?>" style="cursor: pointer" class="remove-user glyphicon glyphicon-remove"></span>
                        </span>
                    <?php endforeach; ?>
                <?php else : ?>                
                    <span id="container-user-<?= $user->id; ?>" class="label label-default" style="margin: 15px;">
                        <label for="user-<?= $user->id; ?>"><?= $user->username; ?></label>
                        <input type="hidden" name="Ticket[users][]" value="<?= $user->id; ?>" id="user-<?= $user->id; ?>"/>
                        <span data-user="<?= $user->id; ?>" style="cursor: pointer" class="remove-user glyphicon glyphicon-remove"></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- end Asignacion de usuarios -->

    </div>
    <!-- end Creación de observaciones -->

    <?= $form->field($model, 'status_id')->widget(DepDrop::class, [
        'options' => [
            'id' =>'status_id',
        ],
        'data' => SchemaDefault::getStatusesForSelect(),
        'type' => DepDrop::TYPE_SELECT2,
        'pluginOptions'=>[
            'depends' => ['category_id'],
            'initDepends' => ['category_id'],
            'initialize' => true,
            'url' => Url::to(['category/get-status-from-schema'])
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \Yii::t('app', 'Create') : \Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

<?= $this->registerJs("Ticket.init();"); ?>
<?= $this->registerJs("Ticket.setCreateObservationInputUrl('" . Url::to(['/ticket/observation/build-observation'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setFindCategoryDefaultDurationUrl('" . Url::to(['/agenda/category/fetch-category'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setCategoriesByTypeUrl('" . Url::to(['/ticket/type/get-categories'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setExternalUsersUrl('" . Url::toRoute(['/ticket/category/get-external-users']) . "');"); ?>
<?= $this->registerJs("Ticket.setGetCategoryResponsibleUserUrl('" . Url::toRoute(['/ticket/category/get-responsible-user-by-category']) . "');"); ?>
<?= $this->registerJs("Ticket.loadExternalUsers();"); ?>


<?php if (!empty($model->users)) : ?>
    <?php foreach ($model->users as $user) : ?>
        <?= $this->registerJs("Ticket.setUser('".$user->id."');"); ?>
    <?php endforeach; ?>
<?php endif; ?>
    
</script>