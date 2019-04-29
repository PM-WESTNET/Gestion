<?php

use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\components\widgets\agenda\ticket\TicketBundle;

$user = webvimark\modules\UserManagement\models\User::findOne(Yii::$app->user->id);
TicketBundle::register($this);
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $categories = \app\modules\ticket\models\Category::getForSelect();
    $aCategories = [];
    $aOptions = [];
    foreach($categories as $category) {
        $aCategories[$category->category_id] = $category->name;
        $aOptions[$category->category_id] = ['data-notify'=> $category->external_user_id ? $category->external_user_id : "0"];
    }
    echo $form->field($model, 'category_id')->widget(Select2::className(),[
        'data' => $aCategories,
        'options' => [
            'placeholder' => Yii::t("app", "Select"),
            'encode' => false,
            'options' => $aOptions,
            'id' => 'category_id'
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);
    ?>

    <div class="form-group" id="div-mesa-user">
        <label class="control-label"><?= \app\modules\ticket\TicketModule::t('app', 'Mesa User'); ?></label>
        <div id="mesa-user"></div>
    </div>
    <!-- Asignacion de usuarios -->
    <div class="panel panel-default" id="user-selection">

        <div class="panel-heading">
            <h3 class="panel-title font-bold"><?= \app\modules\ticket\TicketModule::t('app', 'Assign users to this ticket'); ?></h3>
        </div>

        <div class="panel-body">
            <div class="form-group" style="margin-bottom: 0;">

                <?php
                $userModel = Yii::$app->modules['ticket']->params['user']['class'];
                $users = $userModel::find()
                        ->select(['username as value', 'username as label', 'id as id'])
                        ->where([
                            'status' => webvimark\modules\UserManagement\models\User::STATUS_ACTIVE
                        ])
                        ->asArray()
                        ->all();
                ?>

                <small class="text-muted"><?= \app\modules\ticket\TicketModule::t('app', 'Start writting an username to see more options'); ?></small>
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
                    <?= \app\modules\ticket\TicketModule::t('app', 'User already assigned'); ?>
                </div>

                <?php $userGroups = \app\modules\agenda\models\UserGroup::find()->all(); ?>
                <?php if (!empty($userGroups)) : ?>
                    <div>
                        <?= $form->field($model, 'userGroups')->checkboxList(yii\helpers\ArrayHelper::map($userGroups, 'group_id', 'name')); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="panel-footer">
            <label><?= \app\modules\ticket\TicketModule::t('app', 'Assigned users'); ?></label>
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
            <a class="label label-danger clickable" data-delete-users>
                Eliminar todos los usuarios
            </a>
        </div>
    </div>
    <!-- end Asignacion de usuarios -->

    <!-- Seleccion de cliente -->
    <?php
        echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id']);
    ?>

    <div class="form-group">
        <?php
        if ($model->isNewRecord) {
            $data = [];
        } else {
            $search = new \app\modules\sale\modules\contract\models\search\ContractSearch();
            $data =  $search->findByCustomerForSelect($model->customer_id);
        }
        echo $form->field($model, 'contract_id')->widget(DepDrop::classname(), [
            'options'=>['id'=>'contract_id', 'data-customer-info'=>'data-customer-info-container'],
            'data'=> $data,
            'type'=>DepDrop::TYPE_DEFAULT,
            'pluginOptions'=>[
                'depends' => ['ticket-customer_id'],
                'initDepends' => ['ticket-customer_id'],
                'initialize' => true,
                'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Contract')]),
                'url' => Url::to(['/sale/contract/contract/list-contracts'])
            ]
        ]);
        ?>
    </div>

    <div class="row padding-bottom-half">
        <div class="col-lg-12" data-customer-info-container>
            <?php if(!empty($model->customer)) : 
                
                echo $this->render('../customer/customer_info', [
                    'model' => $model->customer
                ]);
            endif; ?>
        </div>
    </div>
    <!-- end Seleccion de cliente -->

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <!-- Creación de observaciones -->
    <div class="panel panel-default" id="observation-list">

        <div class="panel-heading">
            <h3 class="panel-title font-bold">
                <span class="glyphicon glyphicon-zoom-in"></span> <?= \app\modules\ticket\TicketModule::t('app', 'Add observation'); ?>
            </h3>
        </div>

        <div class="panel-body">
            
            <label><?= \app\modules\ticket\TicketModule::t('app', 'Title'); ?></label>
            <div class="row margin-bottom-quarter">
                <div class="col-lg-12">
                    <input type="text" placeholder="<?= \app\modules\ticket\TicketModule::t('app', 'Observation title'); ?>" data-observation="observation-title" class="form-control" />
                </div>
            </div>
            
            <label><?= \app\modules\ticket\TicketModule::t('app', 'Content'); ?></label>
            <div class="row">
                <div class="col-lg-12">
                    <textarea placeholder="<?= \app\modules\ticket\TicketModule::t('app', 'Observation content'); ?>" data-observation="observation-body" class="form-control"></textarea>
                </div>
            </div>
            
            <div class="form-group margin-top-half">
                <button data-observation="create-observation" data-observation-user="<?= isset($user) && !empty($user) ? $user->username : '' ; ?>" type="button" class="btn btn-info">
                    <?= \app\modules\ticket\TicketModule::t('app', 'Create observation'); ?>
                </button>
            </div>
            
        </div>
        
        <div class="panel-footer">
            <?php if(!empty($model->observations)) : ?>
                <?php foreach($model->observations as $observation) : ?>
            
                    <?= $this->render('../observation/build_observation', [     
                        'old' => true,
                        'username' => $observation->user->username,
                        'title' => $observation->title,
                        'body' => $observation->description,
                        'time' => $observation->datetime,
                    ]); ?>
            
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
    <!-- end Creación de observaciones -->

    <?= $form->field($model, 'status_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\ticket\models\Status::find()->all(), 'status_id', 'name'), ['encode' => false, 'separator' => '<br/>', 'prompt' => \app\modules\ticket\TicketModule::t('app', 'Select an option...')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? \Yii::t('app', 'Create') : \Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

<?= $this->registerJs("Ticket.init();"); ?>
<?= $this->registerJs("Ticket.setCreateObservationInputUrl('" . yii\helpers\Url::to(['/ticket/observation/build-observation'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setFindCategoryDefaultDurationUrl('" . yii\helpers\Url::to(['/agenda/category/fetch-category'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setCustomerInfoUrl('" . yii\helpers\Url::to(['/ticket/customer/get-customer-info'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setCategoriesByTypeUrl('" . yii\helpers\Url::to(['/ticket/type/get-categories'], true) . "');"); ?>
<?= $this->registerJs("Ticket.setExternalUsersUrl('" . yii\helpers\Url::toRoute(['/ticket/category/get-external-users']) . "');"); ?>
<?= $this->registerJs("Ticket.loadExternalUsers();"); ?>


<?php if (!empty($model->users)) : ?>
    <?php foreach ($model->users as $user) : ?>
        <?= $this->registerJs("Ticket.setUser('".$user->id."');"); ?>
    <?php endforeach; ?>
<?php endif; ?>
    
</script>