<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\TaxCondition;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\CustomerCategory;
use app\components\companies\CompanySelector;
use app\modules\sale\models\Customer;
use app\modules\sale\models\HourRange;
use app\modules\sale\models\search\CompanySearch;
use app\modules\sale\models\Company;
use webvimark\modules\UserManagement\models\User;
use kartik\widgets\FileInput;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Customer $model
 * @var yii\widgets\ActiveForm $form
 */
$permiso = Yii::$app->user->identity->hasRole('update-customer-data', false);

?>

<div class="customer-form">
    <?php $form = ActiveForm::begin([
        'id' => 'customer-form'
    ]); ?>
    <div class="row">
<?php if(Yii::$app->user->identity->hasRole('seller', false) || Yii::$app->user->identity->hasRole('seller-office', false)): ?>
    <div class="row">
        <?php if (!$model->isNewRecord) { ?>
            <?= $form->field($model, 'parent_company_id')->hiddenInput()->label('') ?>
        <?php } else {?>
            <?= CompanySelector::widget(['model' => $model, 'attribute' => 'parent_company_id', 'showCompanies' => 'parent', 'conditions' => ['parent_id' => null, 'status'=>'enabled']]) ?>
        <?php } ?>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-sm-6 col-xs-6">
            <?= CompanySelector::widget(['model' => $model, 'attribute' => 'parent_company_id', 'showCompanies' => 'parent', 'inputOptions' => [ 'id' => 'parent_company_id']]) ?>
        </div>
        <div class="col-sm-6 col-xs-6">
            <?php
            $search = new CompanySearch();
            $data = [];
            if(isset($model->parent_company_id)) {
                $data = ArrayHelper::map( Company::findAll(["parent_id"=>$model->parent_company_id]), 'company_id','name');
            }
            ?>
            <div class="form-group field-company_id">
                <?= CompanySelector::widget(['model' => $model, 'attribute' => 'company_id', 'showCompanies' => 'children', 'inputOptions' => [ 'id' => 'company_id']]) ?>
            </div>
        </div>
    </div>
<?php endif; ?>
    <div class="row">
        <div class="col-sm-6 col-xs-6">
            <?php  echo $form->field($model, 'needs_bill')->checkbox(); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'tax_condition_id')->dropDownList(
                ArrayHelper::map(TaxCondition::find()->orderBy(['name'=>SORT_ASC])->all(), 'tax_condition_id', 'name' )) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>
        </div>

        <div class="col-sm-6 col-xs-12 ">
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => 45]) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">

            <?= $form->field($model, 'birthdate')->widget(\yii\jui\DatePicker::class, [
                'clientOptions' => [
                    'format' =>  'dd-mm-yyyy',
                    'autoclose' => true,
                    'maxDate' => Yii::$app->formatter->asDate((time() - ((86400 * 365)* 18)),'dd-MM-yyyy')
                ],
                'options'=>[
                    'class'=>'form-control dates',
                ]
            ]) ?>

        </div>
    </div>

    <div class="row">

        <div class="col-sm-3 col-xs-12">
            <?php
                if (!User::hasRole('superadmin')){
                    $document_types= DocumentType::find()->andWhere(['<>','code', 99]);
                }else{
                    $document_types= DocumentType::find();
                }
            ?>
            <?= $form->field($model, 'document_type_id')->dropDownList( ArrayHelper::map($document_types->all(), 'document_type_id', 'name' )
                , ['id' => 'document_type', 'prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass' => Yii::t('app','Document Type')])] ) ?>
        </div>
        <div class="col-sm-4 col-xs-6">
            <?= $form->field($model, 'document_number')->textInput(['maxlength' => 45, 'id' => 'document_number_input']) ?>
        </div>
        <div class="col-sm-4 col-xs-6 form-group">
            <label class="control-label">&nbsp;</label>
            <div id="div-validation" class="hidden">
                <button id="afip-validation" type="button" class="form-control btn btn-default">Validar en AFIP</button>
                <span id="afip-validation"></span>
            </div>
        </div>
        <div class="col-sm-1 col-xs-6 form-groups">
            <label class="control-label">&nbsp;</label>
            <span id="validation-afip-informer-ok" class="btn glyphicon glyphicon-ok hidden" style="color: green;"></span>
            <span id="validation-afip-informer-error" class="btn glyphicon glyphicon-remove hidden" style="color: red;"></span>
        </div>
    </div>

    <!-- Div para la validación del cliente y deuda-->
    <div class="customer_validation hidden">

    </div>



    <div class="row">
        <div class="col-sm-6 col-xs-12">

            <?= $form->field($model, 'email')->textInput(['maxlength' => 45]) ?>

        </div>
        <div class="col-sm-6 col-xs-12">

            <?= $form->field($model, 'email2')->textInput(['maxlength' => 45]) ?>

        </div>

    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">

            <?= $form->field($model, 'phone')->textInput(['class' => 'form-control phone', 'maxlength' => ($model->isNewRecord ? 10 : 45), 'placeholder'=> 'Ej: 2614XXXXXX']) ?>

        </div>
        <div class="col-sm-6 col-xs-12">

            <?= $form->field($model, 'phone2')->textInput(['class' => 'form-control phone', 'maxlength' => ($model->isNewRecord ? 10 : 45),'placeholder'=> 'Ej: 2616XXXXXX']) ?>

        </div>

    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">

            <?= $form->field($model, 'phone3')->textInput(['class' => 'form-control phone', 'maxlength' => ($model->isNewRecord ? 10 : 45), 'placeholder'=> 'Ej: 2616XXXXXX']) ?>

        </div>
        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'phone4')->textInput(['class' => 'form-control phone', 'maxlength' => ($model->isNewRecord ? 10 : 45), 'placeholder'=> 'Ej: 2616XXXXXX']) ?>

        </div>
    </div>
    <?php if($model->isNewRecord || 
                //User::hasRole('first-data-admin') ||    // is a firstdata admin (most privilegies)
                //User::hasRole('first-data-customer-require') || // role for only showing this field (old implementation but has users attached)
                User::hasPermission('actualizar-clientes-adhesion-debito') // new permission only for showing this field -- will be inherited by some roles
            ):
    ?>
    <div class="row">
        <div class="col-sm-6">
                <?= $form->field($model, 'has_debit_automatic')->dropDownList([
                    'no' => Yii::t('app', 'No'),
                    'yes' => Yii::t('app', 'Yes'),
                ], ['prompt' => $model->isNewRecord ? '' : null])?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
                <?= $form->field($model, 'has_direct_debit')->dropDownList([
                    0 => Yii::t('app', 'No'),
                    1 => Yii::t('app', 'Yes'),
                ], ['prompt' => $model->isNewRecord ? '' : null])?>
        </div>
    </div>
    <?php endif;?>
    <div class="row">
        <div class="col-sm-6 col-xs-12">

            <?php
            //Disabled para sellers
            if($permiso) {
                echo $form->field($model, 'status')->dropDownList(['enabled'=>Yii::t('app','Enabled'),'disabled'=>Yii::t('app','Disabled'),'blocked'=>Yii::t('app','Blocked')]);
            } else {
                $model->status = 'enabled';
                $statusClassOptions['disabled'] = 'disabled';

                echo $form->field($model, 'status')->dropDownList(['enabled'=>Yii::t('app','Enabled'),'disabled'=>Yii::t('app','Disabled'),'blocked'=>Yii::t('app','Blocked')], ['disabled' => 'disabled']);
                echo Html::activeHiddenInput($model, 'status');
            }
            
            ?>
        </div>

    </div>

    <?php 
    //No visible para sellers
    if($permiso) { ?>
    <div class="row">
         <div class="col-sm-12 col-xs-12">
            <?php if (Yii::$app->getModule("accounting")) { ?>
            <div class="form-group field-provider-account">
                <?= $form->field($model, 'account_id')->widget(Select2::className(),[
                    'data' => ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php
            if(Yii::$app->params['class_customer_required']){
                
                //Disabled para sellers
                if(!$permiso){
                    echo $form->field($model, 'customerClass')->label(Yii::t('app', 'Customer Class'))->dropDownList( ArrayHelper::map(CustomerClass::find()->where(['status' => 'enabled'])->all(), 'customer_class_id', 'name' ), ['disabled' => 'disabled']);
                    echo Html::hiddenInput($model->formName()."[customerClass]", ($model->isNewRecord ? CustomerClass::getDefault()->customer_class_id : $model->customerClass->customer_class_id));
                }else{
                    echo $form->field($model, 'customerClass')->label(Yii::t('app', 'Customer Class'))->dropDownList( ArrayHelper::map(CustomerClass::find()->where(['status' => 'enabled'])->all(), 'customer_class_id', 'name' ));
                }

            }
            ?>
        </div>
        <div class="col-sm-6 col-xs-12">
            <?php
            if(Yii::$app->params['category_customer_required']){
                echo $form->field($model, 'customerCategory')->label(Yii::t('app', 'Customer Category'))->dropDownList( ArrayHelper::map(CustomerCategory::find()->where(['status' => 'enabled'])->all(), 'customer_category_id', 'name' ));
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <?php
            echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_reference_id']);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <?= $form->field($model, 'publicity_shape')->widget(Select2::class, [
                    'data' => Customer::getPublicityShapesForSelect(),
                    'value' => $model->publicity_shape,
                    'pluginOptions' => [
                        'placeholder' => Yii::t('app', 'Select ...')
                    ]
                ])
            ?>
        </div>
    </div> 
    <?php
        if ($model->isNewRecord) {
            $model->_notifications_way = ['screen', 'sms', 'email'];
            $model->_sms_fields_notifications = ['phone', 'phone2', 'phone3', 'phone4'];
            $model->_email_fields_notifications= ['email', 'email2'];
        }    
    ?>    
        
    <div class="row" <?=(User::hasRole('seller', false) && !(User::hasRole('seller-office', false)) ? 'style="display: none;"' : '') ?>>
        <div class="col-sm-4 col-xs-4" >
            <?= $form->field($model, '_notifications_way')->checkboxList(Customer::getNotificationWays(), ['id' => 'notifications_way'])?>
        </div>
    
        <div class="col-sm-4 col-xs-5">
            <?= $form->field($model, '_sms_fields_notifications')->checkboxList(Customer::getSMSNotificationWays(), ['id' => 'sms_fields'])?>
        </div>
   
        <div class="col-sm-4 col-xs-3">
            <?= $form->field($model, '_email_fields_notifications')->checkboxList(Customer::getEmailNotificationWays(), ['id' => 'email_fields'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'hourRanges')->checkboxList(HourRange::getHourRangeForCheckList())?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
                <?=Html::hiddenInput('document_image_update', null, ['id'=>'document_image_update']); ?>
                <?= $form->field($model, 'document_image')->widget(FileInput::class, [
                    'pluginOptions' => [
                        'showPreview' => true,
                        'showCaption' => true,
                        'showRemove' => true,
                        'showUpload' => false,
                        'overwriteInitial' => true,
                        'initialPreview'=>($model->document_image ? [Html::img(Yii::$app->request->baseUrl .'/'. $model->getDocumentImageWebPath(), ['class'=>'file-preview-image','style' =>'height:100%; width: 100%', 'alt'=>'', 'title'=>''])] : false ),
                    ]]); ?>
        </div>
        <div class="col-sm-6">
                <?=Html::hiddenInput('tax_image_update', null, ['id'=>'tax_image_update']); ?>
                <?= $form->field($model, 'tax_image')->widget(FileInput::class, [
                    'pluginOptions' => [
                        'showPreview' => true,
                        'showCaption' => true,
                        'showRemove' => true,
                        'showUpload' => false,
                        'overwriteInitial' => true,
                        'initialPreview'=>($model->tax_image ? [Html::img(Yii::$app->request->baseUrl .'/'. $model->getTaxImageWebPath(), ['class'=>'file-preview-image', 'style' =>'height:100%; width: 100%','alt'=>'', 'title'=>''])] : false ),
                    ]]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <?php echo $form->field($model, 'observations')->textarea(['rows' => 3, 'cols' => 10])?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">

          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#address_tab" aria-controls="address_tab" role="tab" data-toggle="tab">Dirección</a></li>
            <li role="presentation"><a href="#profile_tab" aria-controls="profile_tab" role="tab" data-toggle="tab">Perfil</a></li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="address_tab">
                <div class="col-xs-12 no-padding">
                    <?= $this->render('_address', ['address' => $address, 'form' => $form, 'model' => $model]) ?>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="profile_tab">
                <div class="col-xs-12 no-padding">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?= $this->render('_profiles-form',['model'=>$model, 'form'=>$form]); ?>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>

    <?php
        if (!$model->isNewRecord) {
            echo $form->field($model, 'dataVerified')->checkbox();
        }
    ?>

    <div class="row">
        <div class="col-xs-12 no-padding">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>


        </div>
    </div>
    <?php ActiveForm::end(); ?>

    </div>

<script>
    var Customer = new function(){
        var self = this;
        var typeMap = <?= yii\helpers\Json::encode(
                ArrayHelper::map(TaxCondition::find()->all(), 'tax_condition_id', '_documentTypes')
            )
        ?>;

        this.init = function(){

            $('#customer-document_image').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#customer-document_image').val()==0) {
                            $('#document_image_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#customer-document_image').on('filebrowse', function(event) {
                $('#document_image_update').val(1);
            });
            $('#customer-document_image').on('fileclear', function(event) {
                $('#document_image_update').val(1);
            });
            $('#customer-document_image').on('fileselectnone', function(event) {
                $('#document_image_update').val(0);
            });

            $('#customer-tax_image').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#customer-tax_image').val()==0) {
                            $('#tax_image_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#customer-tax_image').on('filebrowse', function(event) {
                $('#tax_image_update').val(1);
            });
            $('#customer-tax_image').on('fileclear', function(event) {
                $('#tax_image_update').val(1);
            });
            $('#customer-tax_image').on('fileselectnone', function(event) {
                $('#tax_image_update').val(0);
            });


            $('#customer-tax_condition_id').on('change', function(e){
                onTaxConditionChange(e);
            });
            
            $.each($('#notifications_way input[type=checkbox]'), function(i, c){
                $(document).on('click', c, function(e){
                    if ($(e.target).val() === 'sms') {
                        if (!$(e.target).is(':checked')) {
                            $.each($('#sms_fields input[type=checkbox]'), function(i, ch){
                                $(ch).prop('checked', false);
                            });                          
                        }else{
                            $.each($('#sms_fields input[type=checkbox]'), function(i, ch){
                                $(ch).prop('checked', true);
                            });       
                        }
                    }else{
                        if ($(e.target).val() === 'email') {
                            if (!$(e.target).is(':checked')) {
                                $.each($('#email_fields input[type=checkbox]'), function(i, ch){
                                    $(ch).prop('checked', false);
                                });                          
                            }else{
                                $.each($('#email_fields input[type=checkbox]'), function(i, ch){
                                    $(ch).prop('checked', true);
                                }); 
                            }
                        }   
                    }
                });
            });

            $(document).off('change', '#document_type').on('change', '#document_type', function(){
                self.changeDocumentType();
            });

            $(document).off('click', '#afip-validation').on('click', '#afip-validation', function(evt){
                evt.preventDefault();
                var $this = $(this);
                $this.button('loading');
                self.afipValidation();
            });

            $(document).on('change', '#document_number_input', function(e){
                Customer.validateCustomer();
            });

            self.changeDocumentType();
            self.phonesMask();
        }

        this.changeDocumentType = function(){
            var options;
            $("#document_number_input").inputmask("remove");
            // Si es CUIT
            if($("#document_type").val()==1) {
                options = 'cuit';
                $('#div-validation').removeClass('hidden');
            } else {
                options = {
                    'mask': '99999999',
                };
                $('#div-validation').addClass('hidden')
            }

            $("#document_number_input").inputmask(options);
        }


        function onTaxConditionChange(e){
            var typeRequired = $(e.target).val();
            
            //if(typeMap[typeRequired]){
            $('#document_type').val(typeMap[typeRequired]);
            self.changeDocumentType();
            //}
        }

        this.afipValidation = function() {
            var cuit = $("#customer-document_number").val();
            $.ajax({
                url: '<?= Url::to(['/sale/customer/afip-validation']) ?>&document=' + cuit ,
                method: 'GET',
            }).done(function(data){
                $('#afip-validation').button('reset');
                $('#customer-email').focus();
                if(data.status){
                  $('#validation-afip-informer-ok').removeClass('hidden');
                  $('#validation-afip-informer-error').addClass('hidden');
                  self.loadFieldsFromAfip(data.data);
                } else {
                   $('#validation-afip-informer-error').removeClass('hidden');
                   $('#validation-afip-informer-ok').addClass('hidden');
                }
            });
        };

        this.loadFieldsFromAfip = function(data) {
            if(data.legal_name !== ''){
                document.getElementById("customer-name").value = data.legal_name;
            } else {
                document.getElementById("customer-name").value = data.name;
            }
            if(data.lastname !== ''){
                document.getElementById("customer-lastname").value = data.lastname;
            }
            if(data.address.address !== ''){
                document.getElementById("address-street").value = data.address.address;
            }
            if(data.address.province !== '' || data.address.location !== '' ){
                document.getElementById("address-indications").value = "Provincia: "+ data.address.province + ", Localidad: "+data.address.location;
            }
            if(data.tax_id !== ''){
                document.getElementById("customer-tax_condition_id").value = data.tax_id;
            }
        };

        /**
         * Verifica si el documento ingresado corresponde a un cliente existente o no.
         * Muestra un mensaje indicando si existe, tiene deuda o es nuevo cliente
         */
        this.validateCustomer = function () {
            $.ajax({
                url: "<?php echo Url::to(['validate-customer'])?>",
                data: {Customer:{document_number: $('#document_number_input').val(), document_type_id:$('#document_type').val() }},
                method: 'POST',
                dataType: 'json'
            }).done(function (response) {
                switch (response.status) {
                    case 'debt':
                        $('.customer_validation').html('<div class="alert alert-danger">'+
                            '<?php echo Yii::t("app","The customer exists in database but he has debt.").' '. Yii::t("app","Debt"). ":"?>'+
                                response.debt +
                            '</div>');
                        $('.customer_validation').removeClass('hidden');
                        break;
                    case 'no_debt' :
                        $('.customer_validation').html('<div class="alert alert-info">'+
                            '<?php echo Yii::t("app","The customer exists in database")?>'+
                            '</div>');
                        $('.customer_validation').removeClass('hidden');
                        break;
                    case 'new':
                        $('.customer_validation').html('<div class="alert alert-success">'+
                            '<?php echo Yii::t("app","The customer not exists in database")?>'+
                            '</div>');
                        $('.customer_validation').removeClass('hidden');
                        break;
                    case 'invalid':
                        $('.customer_validation').html('<div class="alert alert-danger">'+
                            '<?php echo Yii::t("app","Invalid Document Number")?>'+
                            '</div>');
                        $('.customer_validation').removeClass('hidden');
                        break;
                    default:
                        console.log(response);
                        break;
                }
            });
        }

        this.phonesMask = function () {
            $('.phone').inputmask({
                mask: '9999999999'
            })
        }
    };
</script>
<?php $this->registerJs('Customer.init();') ?>

    <script>
        var CompanyForm = new function() {

            this.init = function() {

            };
        };
    </script>
    <?php  $this->registerJs("CompanyForm.init();"); ?>
