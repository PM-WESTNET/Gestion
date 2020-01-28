<?php

use app\modules\accounting\models\Account;
use app\modules\sale\models\TaxCondition;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\modules\employee\models\Employee;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\Employee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12">
            <?php echo \app\components\companies\CompanySelector::widget([
                'form' => $form,
                'model' => $model,
                'setDefaultCompany' => false,
            ])?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'lastname')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?php echo $form->field($model, 'birthday')->widget(\kartik\date\DatePicker::class, [
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd-mm-yyyy'
                ]
            ])?>
        </div>
        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'tax_condition_id')->dropDownList(
                ArrayHelper::map(TaxCondition::find()->orderBy(['name'=>SORT_ASC])->all(), 'tax_condition_id', 'name' )
                ,['id'=>'tax_condition']) ?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'document_type_id')->dropDownList(
                ArrayHelper::map(\app\modules\sale\models\DocumentType::find()->all(), 'document_type_id', 'name' )
                ,['id'=>'tax_condition']) ?>            
        </div>

        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'document_number')->textInput(['maxlength' => 45]) ?>
        </div>

        <div class="col-sm-3 col-xs-6 form-group">
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


    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php if (Yii::$app->getModule("accounting")) { ?>
                <div class="form-group field-employee-account">
                    <?=Html::label(Yii::t('accounting', "Account"), ['account_id'])?>
                    <?=Select2::widget([
                        'model' => $model,
                        'attribute' => 'account_id',
                        'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]);
                    ?>
                </div>
            <?php } ?>
            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => 45]) ?>            
        </div>
    </div>

        <h3><?php echo Yii::t('app','Address')?></h3>
        <?php echo $this->render('../../../sale/views/customer/_address', ['form' => $form, 'address' => $address])?>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    var Employee = new function() {
        var self = this;

        this.init = function() {
            $(document).off('change', '#tax_condition').on('change', '#tax_condition', function(){
                self.changeDocumentType();
            });
            self.changeDocumentType();

            $(document).off('click', '#afip-validation').on('click', '#afip-validation', function(evt){
                evt.preventDefault();
                var $this = $(this);
                $this.button('loading');
                self.afipValidation();
            });
        };

        this.changeDocumentType = function(){
            var options;
            $("#employee-tax_identification").inputmask("remove");
            // Si es CUIT
            if($("#tax_condition").val()!=3) {
                options = 'cuit';
                $('#div-validation').removeClass('hidden');
            } else {
                options = {
                    'mask': '99999999'
                };
            }

            $("#employee-tax_identification").inputmask(options);
        }

        this.afipValidation = function() {
            var cuit = $("#employee-tax_identification").val();
            console.log(cuit);
            $.ajax({
                url: '<?= Url::to(['/employee/employee/afip-validation']) ?>&document=' + cuit ,
                method: 'GET',
            }).done(function(data){
                console.log(data);
                $('#afip-validation').button('reset');
                $('#employee-phone').focus();
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
                document.getElementById("employee-name").value = data.legal_name;
            } else {
                document.getElementById("employee-name").value = data.name + data.lastname;
            }
            if(data.tax_id !== ''){
                document.getElementById("tax_condition").value = data.tax_id;
            }
            if(data.address.address !== ''){
                document.getElementById("employee-address").value = data.address.province + ', '+ data.address.location + ', '+ data.address.address;
            }
            if(data.tax_id !== ''){
                document.getElementById("customer-tax_condition_id").value = data.tax_id;
            }
        };
    };
</script>
<?php $this->registerJs('Employee.init();') ?>