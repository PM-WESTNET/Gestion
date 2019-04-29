<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\TaxCondition;
use kartik\widgets\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>            
        </div>
        <div class="col-xs-12">
            <?= $form->field($model, 'fantasy_name')->textInput(['maxlength' => 255]) ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'partner_distribution_model_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\partner\models\PartnerDistributionModel::find()->all(), 'partner_distribution_model_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>Yii::t('app','Select')]) ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'parent_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\sale\models\Company::find()->all(), 'company_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>Yii::t('app','Select')]) ?>            
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app','Enabled'), 'disabled' => Yii::t('app','Disabled') ]) ?>

            <?= $form->field($model, 'default')->checkbox() ?>            
        </div>        

        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'tax_condition_id')->dropDownList(
                    ArrayHelper::map(TaxCondition::find()->all(), 'tax_condition_id', 'name')
                )->label( $model->getAttributeLabel('taxCondition') ) ?>            
        </div>

        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'tax_identification')->textInput(['maxlength' => 45]) ?>            
        </div>
        <div class="col-sm-3 col-xs-6 form-group">
            <label class="control-label">&nbsp;</label>
            <div id="div-validation" >
                <button id="afip-validation" type="button" class="form-control btn btn-default">Validar en AFIP</button>
                <span id="afip-validation"></span>
            </div>
        </div>
        <div class="col-sm-1 col-xs-6 form-groups">
            <label class="control-label">&nbsp;</label>
            <span id="validation-afip-informer-ok" class="btn glyphicon glyphicon-ok hidden" style="color: green;"></span>
            <span id="validation-afip-informer-error" class="btn glyphicon glyphicon-remove hidden" style="color: red;"></span>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'iibb')->textInput(['maxlength' => 45]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'start')->widget(yii\jui\DatePicker::className(), [
                'language' => Yii::$app->language,
                'model' => $model,
                'attribute' => 'date',
                'dateFormat' => 'dd-MM-yyyy',
                'options'=>[
                    'class'=>'form-control dates',
                    'id' => 'from-date'
                ],
                'clientOptions' => [
                    'changeMonth'=> true,
                    'changeYear' => true,
                    'yearRange' => '-100:c'
                ]
            ]);
            ?>            
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => 45]) ?>            
        </div>

         <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'technical_service_phone')->textInput(['maxlength' => 45]) ?>
        </div>

        <div class="col-sm-12">
            <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?=Html::hiddenInput('certificate_update', null, ['id'=>'certificate_update']); ?>
            <?= $form->field($model, 'certificate')->widget(FileInput::classname(), [
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'overwriteInitial' => true,
                    'initialPreview'=> ($model->certificate ? [$model->certificate] : false ),
                ]]); ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?=Html::hiddenInput('key_update', null, ['id'=>'key_update']); ?>
            <?= $form->field($model, 'key')->widget(FileInput::classname(), [
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'overwriteInitial' => true,
                    'initialPreview'=>($model->key ? [$model->key] : false ),
                ]]); ?>            
        </div>
        <div class="col-xs-12">
            <?= $form->field($model, 'certificate_phrase')->textInput(['maxlength' => 255]) ?>
        </div>

        <div class="col-xs-12">
            <?=Html::hiddenInput('logo_update', null, ['id'=>'logo_update']); ?>
            <?= $form->field($model, 'logo')->widget(FileInput::classname(), [
                'pluginOptions' => [
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'overwriteInitial' => true,
                    'initialPreview'=>($model->logo ? [Html::img(Yii::$app->request->baseUrl .'/'. $model->getLogoWebPath(), ['class'=>'file-preview-image', 'alt'=>'', 'title'=>''])] : false ),
                ]]); ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'billTypes')->checkboxList(yii\helpers\ArrayHelper::map(app\modules\sale\models\BillType::find()->all(), 'bill_type_id', 'name'), ['separator' => '<br/>']) ?>            
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'defaultBillType')->dropDownList(ArrayHelper::map(app\modules\sale\models\BillType::find()->all(), 'bill_type_id', 'name')) ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'code')->textInput() ?>
        </div>

        <div class="row">
            <div class="col-xs-6">
                <?= $form->field($model, 'web')->textInput() ?>
            </div>
            <div class="col-xs-6">
                <?= $form->field($model, 'portal_web')->textInput() ?>
            </div>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'pagomiscuentas_code')->textInput() ?>
        </div>

        <div class="col-xs-12">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>            
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var CompanyForm = new function() {

        this.init = function() {
            $("#company-tax_identification").inputmask('cuit');
            $('#company-tax_condition_id').on('change', function(e){
                changeDocumentType();
            });

            $(document).off('click', '#afip-validation').on('click', '#afip-validation', function(evt){
                evt.preventDefault();
                var $this = $(this);
                $this.button('loading');
                afipValidation();
            });

            $('#company-certificate').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#company-certificate').val()==0) {
                            $('#certificate_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#company-certificate').on('filebrowse', function(event) {
                $('#certificate_update').val(1);
            });
            $('#company-certificate').on('fileclear', function(event) {
                $('#certificate_update').val(1);
            });
            $('#company-certificate').on('fileselectnone', function(event) {
                $('#certificate_update').val(0);
            });

            $('#company-key').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#company-key').val()==0) {
                            $('#key_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#company-key').on('filebrowse', function(event) {
                $('#key_update').val(1);
            });
            $('#company-key').on('fileclear', function(event) {
                $('#key_update').val(1);
            });
            $('#company-key').on('fileselectnone', function(event) {
                $('#key_update').val(0);
            });

            $('#company-logo').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#company-logo').val()==0) {
                            $('#logo_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#company-logo').on('filebrowse', function(event) {
                $('#logo_update').val(1);
            });
            $('#company-logo').on('fileclear', function(event) {
                $('#logo_update').val(1);
            });
            $('#company-logo').on('fileselectnone', function(event) {
                $('#logo_update').val(0);
            });


            defaultTypes();
            $('#company-billtypes input').on('click', function(){
                defaultTypes();
            });
        };
    };

    function defaultTypes(){
        $('#company-defaultbilltype option').attr('disabled','disabled');
        $('#company-billtypes :checked').each(function (index, element) {
            $('#company-defaultbilltype [value='+$(element).val()+']').removeAttr('disabled');
        });
    }

    this.changeDocumentType = function(){
        var options;
        $("#company-tax_identification").inputmask("remove");
        // Si es CUIT
        if($("#company-tax_condition_id").val()==3) {
             options = {
                'mask': '99999999'
            };
            $('#div-validation').addClass('hidden');
        } else {
            options = 'cuit';
            $('#div-validation').removeClass('hidden');
        };

        $("#company-tax_identification").inputmask(options);
    };

    this.afipValidation = function() {
            var cuit = $("#company-tax_identification").val();
            $.ajax({
                url: '<?= Url::to(['/sale/company/afip-validation']) ?>&document=' + cuit ,
                method: 'GET'
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
                $('#company-name').val(data.legal_name);
            }
            if(data.name !== '' && data.lastname !== ''){
                $('#company-name').val(data.name);
            } else {
                if(data.name !== ''){
                    $('#company-name').val(data.name);                }
                if(data.lastname !== ''){
                    $('#company-name').val(data.lastname);
                }
            }
            if(data.address.province !== '' || data.address.location !== '' || data.address.address !== ''){
                document.getElementById("company-address").value = data.address.province + ", "+data.address.location + ', '+data.address.address;
            }
            if(data.tax_id !== ''){
                document.getElementById("company-tax_condition_id").value = data.tax_id;
            }
        };

</script>
<?php  $this->registerJs("CompanyForm.init();"); ?>