<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\DocumentType;
use kartik\select2\Select2;
use app\modules\accounting\models\Account;
use app\modules\sale\models\Company;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use webvimark\extensions\BootstrapSwitch\BootstrapSwitch;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Vendor */
/* @var $user app\components\user\User*/
/* @var $address app\modules\sale\models\Address*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-vendor-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->errorSummary($model) ?>
   
    <div class="row">
        <div class="col-sm-6">
           <?= $form->field($model, 'name')->label(Yii::t('app','Name'))->textInput(['maxlength' => 150]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'lastname')->label(Yii::t('app','Lastname'))->textInput(['maxlength' => 45]) ?>
        </div>
    </div>
    <div class="row">
        <?php if ( $model->isNewRecord ): ?>
            <div class="col-sm-6">
                <?= $form->field($user, 'username')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
            </div>
        <?php endif; ?>
        <div class="col-sm-6">
            <?= $form->field($user->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>
        </div>
    </div>

	<?php if ( $model->isNewRecord || empty($model->user)): ?>
            <div class="row">
                <div class="col-sm-6">
                  <?= $form->field($user, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($user, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
                </div>
            </div>
	<?php endif; ?>
    
    <?php if ( User::hasPermission('bindUserToIp') ): ?>

		<?= $form->field($user, 'bind_to_ip')
			->textInput(['maxlength' => 255])
			->hint(UserManagementModule::t('back','For example: 123.34.56.78, 168.111.192.12')) ?>

	<?php endif; ?>

    
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'document_type_id')->dropDownList( ArrayHelper::map(DocumentType::find()->all(), 'document_type_id', 'name' )
                    , ['id' => 'document_type', 'prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass' => Yii::t('app','Document Type')])] ) ?>
        </div>
        <div class="col-sm-9">
            <?= $form->field($model, 'document_number')->textInput(['maxlength' => 45]) ?>
        </div>
    </div>

    
    <?= $form->field($user, 'email')->textInput(['maxlength' => 45]) ?>
    
    <?= $form->field($model, 'sex')->dropDownList(['female'=>Yii::t('app','Female'),'male'=>Yii::t('app','Male')]) ?>
    
    <?= $form->field($model, 'phone')->textInput(['maxlength' => 45]) ?>

    <?php if (Yii::$app->getModule("accounting")) { ?>
    <div class="form-group field-provider-account">
        <?= $form->field($model, 'account_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>
    <?php } ?>
    
    <?= $form->field($model, 'vendor_commission_id')->dropDownList(app\modules\westnet\models\VendorCommission::findForSelect()) ?>

    <?php
        echo $form->field($model, 'external_user_id')->dropDownList([], ['id'=>'external_user_id']);

        echo $form->field($model, 'provider_id')->label(isset($label) ? $label : null)->widget(Select2::classname(), [
            'options' => ['placeholder' => Yii::t('app', 'Search')],
            'initValueText' => ($model ? ( $model->provider ? $model->provider->name  :  '' ) : '' ),
            // 'value' => ($model->vendor->provider ? $model->vendor->provider_id: '0' ),
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                    'url' => Url::to(['/provider/provider/find-by-name']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {name:params.term, id:$(this).val()}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(provider) { return provider.text; }'),
                'templateSelection' => new JsExpression('function (provider) { return provider.text; }'),
                'cache' => true
            ],
        ]);

    ?>

    <?php if ( User::hasPermission('assign-access-company-vendor') ){
        echo $form->field($user, 'companies')->widget(Select2::class,[
        'data' => ArrayHelper::map(Company::getParentCompanies(), 'company_id', 'name'),
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true
        ]
        ]);
    } ?>

    <?= $this->render('_address', ['address' => $address, 'form' => $form, 'model' => $model]) ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

    <script>
        var VendorForm = new function(){
            this.init = function(){
                VendorForm.loadUsers();
            }

            this.loadUsers = function(){
                $.ajax({
                    url: '<?php echo Url::toRoute(['/ticket/category/get-external-users'])?>',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data){
                        var $select = $('#external_user_id');
                        $select.find('option').remove();
                        $('<option>').val('').text('<?php echo Yii::t('app', 'Select')  ?>').appendTo($select);

                        $.each(data, function(key,item){
                            $('<option>').val(item.id).text(item.nombre).appendTo($select);
                        });
                        $select.val(<?php echo ($model ?  $model->external_user_id : "0") ?>);
                    }
                });
            }
        }
    </script>
<?php $this->registerJs("VendorForm.init();"); ?>