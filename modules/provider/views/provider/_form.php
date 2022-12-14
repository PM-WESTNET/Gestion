<?php

use app\modules\accounting\models\Account;
use app\modules\sale\models\TaxCondition;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\modules\provider\models\Provider;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\Provider */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provider-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'business_name')->textInput(['maxlength' => 255]) ?>            
        </div>

        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'tax_condition_id')->dropDownList(
                ArrayHelper::map(TaxCondition::find()->orderBy(['name'=>SORT_ASC])->all(), 'tax_condition_id', 'name' )
                ,['id'=>'tax_condition']) ?>            
        </div>

        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'tax_identification')->textInput(['maxlength' => 45]) ?>            
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

        <div class="col-sm-8 col-xs-12">
            <?php if (Yii::$app->getModule("accounting")) { ?>
                <div class="form-group field-provider-account">
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
        <div class="col-sm-4 col-xs-12">
            <?= $form->field($model, 'bill_type')->dropDownList(Provider::getAllBillTypes()) ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => 45]) ?>            
        </div>

        <div class="col-sm-6 col-xs-12">
            <?= $form->field($model, 'phone2')->textInput(['maxlength' => 45]) ?>            
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>            
        </div>

        <div class="col-xs-12">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
            </div>            
        </div>
        
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    var Provider = new function() {
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
            $("#provider-tax_identification").inputmask("remove");
            // Si es CUIT
            if($("#tax_condition").val()!=3) {
                options = 'cuit';
                $('#div-validation').removeClass('hidden');
            } else {
                options = {
                    'mask': '99999999'
                };
            }

            $("#provider-tax_identification").inputmask(options);
        }

        this.afipValidation = function() {
            var cuit = $("#provider-tax_identification").val();
            console.log(cuit);
            $.ajax({
                url: '<?= Url::to(['/provider/provider/afip-validation']) ?>&document=' + cuit ,
                method: 'GET',
            }).done(function(data){
                console.log(data);
                $('#afip-validation').button('reset');
                $('#provider-phone').focus();
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
                document.getElementById("provider-name").value = data.legal_name;
            } else {
                document.getElementById("provider-name").value = data.name + data.lastname;
            }
            if(data.tax_id !== ''){
                document.getElementById("tax_condition").value = data.tax_id;
            }
            if(data.address.address !== ''){
                document.getElementById("provider-address").value = data.address.province + ', '+ data.address.location + ', '+ data.address.address;
            }
            if(data.tax_id !== ''){
                document.getElementById("customer-tax_condition_id").value = data.tax_id;
            }
        };
    };
</script>
<?php $this->registerJs('Provider.init();') ?>