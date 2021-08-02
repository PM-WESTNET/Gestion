<?php

use app\modules\sale\models\Product;
use app\modules\sale\models\search\FundingPlanSearch;
use app\modules\sale\modules\contract\models\Contract;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\zone\models\Zone;
use app\modules\westnet\models\Vendor;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contract-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation'=> false,
        'id' => 'contractForm',
    ]); ?>
    <?= Html::hiddenInput('ContractDetail[contract_id]', $model->contract_id) ?>
    <?= Html::hiddenInput('ContractDetail[status]', $model->status, ['id'=>'contract_detail_status']) ?>
    <?= Html::hiddenInput('old_product_id', $contractDetailPlan->product_id, ['id'=>'old_product_id']) ?>
    <input type="hidden" name="is_customer" id="is_customer" value="-1"/>

    <?php 
    //Si o si debe haber un vendedor asignado
    if(User::hasPermission('user-can-select-vendor') || $contractDetailPlan->vendor_id === null): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('westnet','Vendor'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <?php
                $select = [];
                foreach($vendors as $vendor){
                    $select[$vendor->vendor_id] = "$vendor->lastname, $vendor->name";
                }
                
                echo $form->field($model, 'vendor_id')->dropDownList($select, ['prompt' => '']) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    
    <?php if(User::hasPermission('user-view-plans')): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app','Plan'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($contractDetailPlan, 'product_id')->dropdownList(ArrayHelper::map($plans,
                            'product_id', function($plan){ return $plan->name.' - $'.round($plan->finalPrice,2); }),['encode'=>false, 'separator'=>'<br/>','prompt'=>Yii::t('app', 'Select an option...'), 'id'=> 'plan_product_id']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (isset($contractDetailPlan->discount) && $contractDetailPlan->discount!==null) {
                            $data = [$contractDetailPlan->discount->discount_id=>$contractDetailPlan->discount->name];
                        } else {
                            $data = [];
                        }

                        echo $form->field($contractDetailPlan, 'discount_id')->widget(DepDrop::className(), [
                            'options' => ['id' => 'discount_id'],
                            'data' => $data,
                            //'type'=>DepDrop::TYPE_SELECT2,
                            'select2Options'=>[
                                'pluginOptions'=>[
                                    'allowClear'=>true,
                                ],
                            ],
                            'pluginOptions' => [
                                'depends' => ['plan_product_id', 'is_customer'],
                                'initDepends' => ['plan_product_id', 'is_customer'],
                                'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Discount')])."...",
                                'url' => Url::to(['/sale/discount/discount-by-product'])
                            ]
                        ])->label(Yii::t('app', 'Discount'));
                        ?>
                    </div>
                </div>
                <?php if($model->isNewRecord):?>
                <?= $form->field($contractDetailIns, 'count')->hiddenInput(['id' => 'instCount', 'name'=>'contractDetailIns[count]', 'value' => '1'])->label('')?>
                <div class="row">
                    <input type="hidden" name="is_customer_detail" id="is_customer_detail" value="0"/>
                    <div class="col-md-4">
                        <?=
                           $form->field($contractDetailIns, 'product_id')->dropdownList(ArrayHelper::map($instalationProd,
                            'product_id', function($prod){ return $prod->name.' - $'.round($prod->finalPrice,2); }),['encode'=>false, 'separator'=>'<br/>','prompt'=>Yii::t('app', 'Select an option...'), 'id'=> 'inst_product_id', 'name'=>'contractDetailIns[product_id]'])->label(Yii::t('app', 'Instalation Charges')) ?>
                    </div>
                   
                    
                    
                    <div class="col-md-4">
                        <?php
                            if ($contractDetailIns->isNewRecord) {
                                $data = [];
                            } else {
                                $search = new FundingPlanSearch();
                                $data =  ArrayHelper::map($search->searchByProduct($contractDetailIns->product_id, 1), 'id', 'name' );
                            }
                            echo $form->field($contractDetailIns, 'funding_plan_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'funding_plan_id', 'name'=>'contractDetailIns[funding_plan_id]'],
                                'data'=> $data,
                                'type'=>DepDrop::TYPE_SELECT2,
                                'pluginOptions'=>[
                                   'depends' => ['inst_product_id', 'instCount'],
                                   'initDepends' => ['inst_product_id', 'instCount'],
                                   'initialize' => true,
                                   'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Funding Plan')]),
                                   'url' => Url::to(['/sale/contract/contract/funding-plans'])
                                ]
                            ]);
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?php
                            if ($model->isNewRecord) {
                                $data = [];
                            } else {
                                if($model->discount) {
                                    $data = [$model->discount->discount_id=>$model->discount->name];
                                } else {
                                    $data = [];
                                }
                            }
                            echo $form->field($contractDetailIns, 'tmp_discount_id')->widget(DepDrop::className(), [
                                'options' => ['id' => 'tmp_discount_id', 'name'=>'contractDetailIns[discount_id]'],
                                'data' => $data,
                                'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
                                'pluginOptions' => [
                                    'loading' => true,
                                    'depends' => ['inst_product_id', 'is_customer_detail'],
                                    'initDepends' => ['inst_product_id', 'is_contract_detail'],
                                    'initialize' => true,
                                    'placeholder' =>  Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Discount')]),
                                    'url' => Url::to(['/sale/discount/discount-by-product'])
                                ]
                            ])->label(Yii::t('app', 'Discount'));
                            ?>
                    </div>
                        
                </div>        
                   <?php endif;?>     
                    
               
                <?php if(!$model->isNewRecord && (User::hasRole('seller-office') || (User::hasPermission('update-contract') && !User::hasRole('seller')))):?>
                <div class="row" id="divPlanFromDate">
                    <div class="col-md-6">
                        <?=$form->field($contractDetailPlan, 'from_date')->widget(DatePicker::classname(), [
                            'type' => 1,
                            'language' => Yii::$app->language,
                            'model' => $contractDetailPlan,
                            'attribute' => 'from_date',
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'dd-mm-yyyy',
                            ],
                            'options'=>[
                                'class'=>'form-control filter dates',
                                'placeholder'=>Yii::t('app','Date'),
                                'id' => 'plan_from_date',
                                 
                            ]
                        ])->label(Yii::t('app', 'Start date for the new plan'));
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif;?>
    
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'instalation_schedule')->dropDownList(['in the morning' => Yii::t('app', 'In the Morning'), 'in the afternoon' => Yii::t('app', 'In the Afternoon'), 'all day' => Yii::t('app', 'All day')], ['prompt' => Yii::t('app', 'Select an option...')] )?>
        </div>
    </div>
    
    <?php if ((User::hasPermission('update-contract-detail')&& !$contractDetailPlan->isNewRecord) || (User::hasPermission('create-contract-detail')&& $contractDetailPlan->isNewRecord)):?>
   
    <!-- Carga de adicionales -->
    <div class="row" id="additionals">
    </div>
    <?php endif;?>
    <div id='select_address' class="select_address">
        <label><?= Html::checkbox('same_address', ($address ? ($address->address_id == $model->customer->address_id && $model->contract_id !== null? true : false) : false), ['id'=>'same_address'])?><?= Yii::t('app', 'Misma dirección que el cliente')?></label>
        <br>
        
    </div>
    <br>
    <?= Html::hiddenInput('Address[address_id]', $address->address_id) ?>
    <?= $this->render('@app/modules/sale/views/customer/_address', ['form' => $form, 'address' => $address, 'hideMap' => false]); ?>
    
    <div class="form-group">
        <?php
        if ($model->isNewRecord) {
            echo Html::submitButton(Yii::t('app', 'Create and finish'), ['class' => 'btn btn-success', 'id'=> 'finish']);
            echo ' ';
            echo Html::submitButton(Yii::t('app', 'Create and add aditionals'), ['class' => 'btn btn-info', 'id'=> 'continue']);
        } else {
            echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']);
        }
        ?>
        <?php
            if (!$model->isNewRecord) {
                echo Html::a(Yii::t('app', 'Cancel'), ['view', 'id' => $model->contract_id], ['class' => 'btn btn-danger']);
            }?>
    </div>
    
    <input type='hidden' name='mode' id="mode">
    <?php ActiveForm::end(); ?>

</div>
<script>
    var ContractForm = new function() {
        

        this.old_address_id = 0;
        this.init = function () {

            $('#continue').attr('disabled', false);
            $('#finish').attr('disabled', false);

            $(document).off('change','#same_address').on('change','#same_address', function() {
                ContractForm.sameAddress();
            });
            $(document).off('click', '#agregar').on('click', '#agregar', function(){
                ContractForm.agregarAdicional();
            });
            $(document).off('click', '.remove-additional').on('click', '.remove-additional', function(){
                ContractForm.removeAdicional(this);
            });
            $(document).off('click', '.update-additional').on('click', '.update-additional', function(){
                ContractForm.updateAdicional(this);
            });
            $(document).off('click', '.change-status-additional').on('click', '.change-status-additional', function(){
                ContractForm.changeStatusAdicional(this);
            });
            $(document).off('change', '#plan_product_id').on('change', '#plan_product_id', function(){
                ContractForm.changeProduct();
            });
            $(document).off('change', '#product_id').on('change', '#product_id', function(){
                ContractForm.changeProductoAdicional();
            });

            $(document).off('click', '#continue').on('click', '#continue', function(e){
                e.preventDefault();
                $('#mode').val(1);
                $(this).attr('disabled', true);
                $('#contractForm').submit();
            });

            $(document).off('click', '#finish').on('click', '#finish', function(e){
                    e.preventDefault();
                    $('#mode').val(0);
                    $(this).attr('disabled', true);
                    $('#contractForm').submit();
            });


            ContractForm.showAdditionals();
            ContractForm.changeProduct();

            if(<?= (($model->customer->address_id ? $address->address_id == $model->customer->address_id : false ) ? 'true' : 'false' )?>) {
                $("#address").collapse('show');
            } else {
                $("#address").collapse('hide');
            }
            ContractForm.sameAddress();
            // Esto es para que no valide los campos que estan marcados como disabled
            $('form').on('beforeValidateAttribute', function (event, attribute) {
                if ($(attribute.input).prop('disabled')) { return false; }
            });

            $('#discount_id').on('depdrop.afterChange', function(event, id, value) {
                if($(this).find('option').length == 1) {
                    $($('#discount_id').find('option[value=""]')[0]).html('<?php echo Yii::t('app', 'No discounts are available')?>')
                }
            });
            
            $('#tmp_discount_id').on('depdrop.afterChange', function(event, id, value) {
                if($(this).find('option').length == 1) {
                    $($('#tmp_discount_id').find('option[value=""]')[0]).html('<?php echo Yii::t('app', 'No discounts are available')?>')
                }
            });

        }
        
        

        this.showAdditionals = function(contract_detail_id) {
            var contract_detail_id = (contract_detail_id ? "&contract_detail_id=" + contract_detail_id : "" );
            $.ajax({
                url: '<?=Url::to(['/sale/contract/contract/show-additionals', 'contract_id'=>$model->contract_id])?>'+contract_detail_id,
                type: 'post',
                dataType: 'html'
            }).done(function(data){
                $("#additionals").html(data);
                if($('#contract_detail_status').val()==='<?php echo Contract::STATUS_DRAFT?>') {
                    $('#divDateFrom,#divDateTo').hide();
                    $('#contractdetail-from_date').attr('disabled', 'disabled');
                    $('#contractdetail-to_date').attr('disabled', 'disabled');
                } else {
                    $('#divDateFrom,#divDateTo').show();
                    $('#contractdetail-from_date').removeAttr('disabled');
                    $('#contractdetail-to_date').removeAttr('disabled');
                }
            });
        }

        //TODO: validar antes de enviar. Ver al final (CUIDADO: METODO ASINCRONO!!) https://github.com/yiisoft/yii2/issues/9500
        this.agregarAdicional = function() {
            $.ajax({
                url: '<?=Url::to(['/sale/contract/contract/add-contract-detail', 'id'=>$model->contract_id])?>',
                data:$("#form-additional").serializeArray(),
                type: 'post',
                dataType: 'json'
            }).done(function(data){
                if(data.status === 'success'){
                    ContractForm.showAdditionals();
                }else{
                    $('#message').empty()
                    $.each(data.errors, function(i, e){
                        $('#message').append('<div class="alert alert-danger">'+ e + '</div>');
                    });
                }
            });
        }

        this.removeAdicional = function(element) {
            var id = $(element).data('id');
            if (confirm($(element).data('confirms'))) {
                $.ajax({
                    url: '<?=Url::to(['/sale/contract/contract/remove-contract-detail'])?>',
                    data: {
                        'id': id
                    },
                    type: 'post',
                    dataType: 'json'
                }).done(function (data) {
                    ContractForm.showAdditionals();
                });
            }
        }

        this.changeStatusAdicional = function(element) {
            var id = $(element).data('id');
            var to_status = $(element).data('to-status');
            if (confirm($(element).data('confirms'))) {
                $.ajax({
                    url: '<?=Url::to(['/sale/contract/contract/change-status-contract-detail'])?>',
                    data: {
                        'id': id,
                        'to_state':to_status
                    },
                    type: 'post',
                    dataType: 'json'
                }).done(function (data) {
                    ContractForm.showAdditionals();
                });
            }
        }

        this.updateAdicional = function(element) {
            var id = $(element).data('id');
            ContractForm.showAdditionals(id);
        }

        this.sameAddress = function() {
            if($("#same_address").is(':checked')) {
                $("#address").hide();
                $("#address input, #address select").attr("disabled", "disabled");
                $("#address #address_id").val(ContractForm.old_address_id);
            } else {
                $("#address").show();
                if(<?=($address->address_id == $model->customer->address_id?'true': 'false')?>) {
                    ContractForm.old_address_id = $("#address #address_id").val();
                    //$("#address input").val('');
                    //$("#address select").val(0);
                }
                $("#address input, #address select").removeAttr("disabled");
                CustomerMap.map();
            }
        }

        this.changeProduct = function() {
            if( ( $('#old_product_id').val() != $('#plan_product_id').val() &&
                $('#contract_detail_status').val()!='<?php echo Contract::STATUS_DRAFT?>' &&
                $('#old_product_id').val() != '') /** <?php echo ((count($contractDetailPlan->getErrors())>0 ) ? 'true' : 'false' );?> **/) {
                $('#divPlanFromDate').show()
                $('#plan_from_date').removeAttr('disabled');
                $('#plan_from_date').val('');
            } /**else {
                $('#divPlanFromDate').hide()
                $('#plan_from_date').attr('disabled', 'disabled');
            }**/
        }

        this.changeProductoAdicional = function(){
            if ($('#product_id option:selected').data('type') == 'product') {
                $('#divDateTo').hide()
                $('#contractdetail-to_date').attr('disabled', 'disabled');
            } else if ($('#product_id option:selected').data('type') !== 'service') {
                $('#divDateFrom').hide()
                $('#contractdetail-from_date').attr('disabled', 'disabled');
                $('#divDateTo').hide()
                $('#contractdetail-to_date').attr('disabled', 'disabled');
            }
        }
        
        
    }
</script>

<?php $this->registerJs('ContractForm.init();');?>