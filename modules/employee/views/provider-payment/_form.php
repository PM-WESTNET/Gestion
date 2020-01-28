<?php

use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\employee\models\Employee;
use app\modules\employee\models\EmployeeBillHasEmployeePayment;
use kartik\widgets\Select2;
use yii\data\ActiveDataEmployee;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use \app\modules\employee\models\EmployeePaymentItem;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeePayment */
/* @var $form yii\widgets\ActiveForm */

$payment_method_bank = Config::getValue('payment_method_bank');;
$payment_method_paycheck = Config::getValue('payment_method_paycheck');
$payment_method_cash = Config::getValue('payment_method_cash');

?>

<div class="employee-payment-form">

    <?php
        $form = ActiveForm::begin(['id'=>'employee-payment-form']);
        echo Html::hiddenInput('EmployeePayment[employee_payment_id]', $model->employee_payment_id, ['id'=>'employee_payment_id']);
    ?>
    
    <?= Html::hiddenInput('EmployeePayment[status]', $model->status, ['id'=>'payment_status']) ?>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?php
    echo $this->render('@app/modules/partner/views/partner-distribution-model/_selector', ['model' => $model, 'form'=>$form]);
    ?>

    
    <div class="form-group<?= $model->hasErrors('employee_id') ? ' has-error' : '' ?>">
        <?php
        if (!$model->employee) {
            ?>
            <div class="input-group" style="z-index:0;">
                <?= Html::label(Yii::t('app', "Employee"), ['employee_id']) ?>
                <?=Select2::widget([
                    'model' => $model,
                    'attribute' => 'employee_id',
                    'data' => yii\helpers\ArrayHelper::map(Employee::find()->all(), 'employee_id', 'name' ),
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                echo Html::error($model, 'employee_id', ['class' => 'help-block']);
                ?>
            </div>
        <?php } else {
            echo Html::activeLabel($model, 'employee') ;
            echo '<div class="form-control filter dates hasDatepicker">'.$model->employee->name.'</div>';
        } ?>
    </div>


    <div class="form-group">
        <?= Html::activeLabel($model, 'date'); ?>
        <?php 
        echo yii\jui\DatePicker::widget([
            'language' => Yii::$app->language,
            'model' => $model,
            'attribute' => 'date',
            'dateFormat' => 'dd-MM-yyyy',
            'options'=>[
                'class'=>'form-control filter dates',
                'placeholder'=>Yii::t('app','Date'),
                'autocomplete' => "off"
            ],
            'clientOptions' => [
                    'onSelect' => new \yii\web\JsExpression('function(dateText, inst) { return EmployeePayment.changeDate(dateText, inst) }')
            ]
        ]);
        ?>
    </div>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>
    <?php ActiveForm::end(); ?>

    <?php
    if(!$model->isNewRecord) {
        ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Detail') ?></h3>
        </div>
        <div class="panel-body">
            <?php
                echo $this->render('_payment-items', ['model'=>$model, 'item'=>new EmployeePaymentItem()]);
                // Listado de detalles
                \yii\widgets\Pjax::begin(['id'=>'w_items']);

                $dataProvider = new ActiveDataProvider([
                    'query' => $model->getEmployeePaymentItems()
                ]);
            ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- Grid -->
                    <?php
                    echo GridView::widget([
                        'id'=>'grid',
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'label' => Yii::t('app', 'Payment Method'),
                                'value' => function($model){
                                    return $model->paymentMethod->name .
                                    ($model->moneyBoxAccount ? " - " . $model->moneyBoxAccount->moneyBox->name : '' ) .
                                    ($model->moneyBoxAccount ? " - " . $model->moneyBoxAccount->number : '' ) .
                                    ($model->number ? " - " . $model->number : '' ) ;
                                },
                            ],
                            'description',
                            'amount:currency',
                            [
                                'class' => 'app\components\grid\ActionColumn',
                                'template'=>'{delete}',
                                'buttons'=>[
                                    'delete'=>function ($url, $model, $key) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', "#",
                                            [
                                                'data-url' => yii\helpers\Url::toRoute(['employee-payment/delete-item', 'employee_payment_item_id'=>$key]),
                                                'title' => Yii::t('yii', 'Delete'),
                                                'data-confirms' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                'class' => 'payment-item-delete btn btn-danger'
                                            ]
                                        );
                                    }
                                ]
                            ],
                        ],
                        'options'=>[
                            'style'=>'margin-top:10px;'
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <div class="row text-center" id="totals">
                <div class="col-sm-9 col-md-3 col-md-offset-9 ">
                    <label><?=Yii::t("app", "Total of Payment")?></label>
                    <div><label><?=Yii::$app->formatter->asCurrency($model->calculateTotal())?></label></div>
                </div>
            </div>
            <?php \yii\widgets\Pjax::end(); ?>

        </div>

    </div>
        <?php
            echo $this->render('_form-bills', ['model'=>$model,'dataProvider'=>$billDataProvider, 'pbp'=>new EmployeeBillHasEmployeePayment()]);
        ?>
    <?php
    }
    ?>

    <div class="form-group" id="saveButtons">
        <a id="closePayment" class="btn btn-success" style="display:<?=($model->canClose() ? '' : 'none' ) ?>">
            <span class="glyphicon glyphicon-ok"></span>
            <?= Yii::t('app','Close Payment'); ?>
        </a>
        <a id="savePayment" class="btn btn-success"><?= Yii::t('app',($model->isNewRecord ? 'Next' : 'Add Detail')); ?></a>
        <?php if(!$model->isNewRecord) {
            echo Html::a(Yii::t('app', 'Save draft'),['view' , 'id' => $model->employee_payment_id], [
                'class' => 'btn btn-warning',
            ]);
        }?>
    </div>
    <input type="hidden" id="employee_bill_amount" value="<?php echo $model->calculateTotalPayed()?>" />
    <input type="hidden" id="amount_total" value="<?php echo $model->amount?>" />
</div>
<script>
    var EmployeePayment = new function() {

        this.init = function() {

            $(document).on("click", "#bill-add", function(){
                EmployeePayment.addBill();
            });

            $(document).off('click', '#item-add')
                .on('click', '#item-add', function(){
                    EmployeePayment.addItem();
                });
            $(document).off('click', '.payment-item-delete')
                .on('click', '.payment-item-delete', function(){
                    EmployeePayment.removeItem(this);
                });

            $(document).on("keyup", "#employee_bill_amount", function(){
                EmployeePayment.validateAmount();
            });

            $(document).off("click", "#closePayment")
                .on("click", "#closePayment", function(){
                EmployeePayment.close();
            });
            $(document).off("click", "#savePayment")
                .on("click", "#savePayment", function(){
                EmployeePayment.save();
            });

            $(document).off('click', '.checkbill')
                .on('click', '.checkbill', function(){
                var input = $(this).closest('tr').find('.total_amount');
                if($(this).is(':checked')) {
                    input.removeAttr('disabled');
                    input.val(input.data('max'));
                    EmployeePayment.addBill(this);
                } else {
                    input.attr('disabled', true);
                    input.val(0);
                    EmployeePayment.removeBill(this);
                }
            });

            $(document).off('blur', '.total_amount')
                .on('blur', '.total_amount', function(event){
                    event.stopPropagation();
                    EmployeePayment.updateAmount(this);
            });

            $(document).off('keypress', '.total_amount')
                .on('keypress', '.total_amount', function(e){
                    e.stopPropagation();
                    if((e.keyCode || e.which) == 13) {
                        EmployeePayment.updateAmount(this);
                    }

                });

            $("#payment_method_id").trigger("change");


            <?php if (Yii::$app->getModule('paycheck') && $model->employee_payment_id ) { ?>
                SearchPaycheck.onSelect = function (json){
                    $("#employeepayment-amount").val(json.paycheck.amount);
                }
            <?php } ?>

            $(document).off('change', '#employeepayment-date').on('change', '#employeepayment-date', function(){
                $('.ui-datepicker-current-day').click();
            });
        }

        this.save = function() {
            $("#employee-payment-form").submit();
        }

        this.close = function() {
            if (confirm('<?=Yii::t('app', 'Are you sure you want to close the Payment?' )?>')) {
                $("#employee-payment-form").attr('action', "<?=Url::to(['/employee/employee-payment/close', 'id'=>$model->employee_payment_id])?>" );
                $("#employee-payment-form").submit();
            }
        }

        this.validateAmount = function() {
            var billAmount  = parseFloat($("#employee_bill_amount").val());
            var amountTotal = parseFloat($("#amount_total").val());

            $("#message").hide();
            if (amountTotal > (billAmount)) {
                $("#message").show();
                return false;
            } else if(billAmount==0) {
                return false;
            } else {
                return true;
            }
        }

        this.updateAmount = function(elem) {
            var max = new Number($(elem).data('max'));
            var val = new Number($(elem).val());
            if(val <= 0) {
                $(elem).closest('tr').find('.checkbill').trigger('click');
            } else {
                if(max && (val > max) && max != 0 ) {
                    alert("El valor ingresado es mayor al maximo de la factura.");
                    $(elem).val(max)
                    return false;
                } else {
                    EmployeePayment.addBill($(elem).closest('tr').find('.checkbill'));
                }
            }
        }


        this.addBill = function(elem) {
            var data = {
                'EmployeeBillHasEmployeePayment': {
                    'employee_bill_id': $(elem).val(),
                    'employee_payment_id': <?php echo ($model->employee_payment_id ? $model->employee_payment_id: "''" )  ?>,
                    'amount': $(elem).closest('tr').find('.total_amount').val()
                }
            };

            $.ajax({
                url: '<?=Url::to(['/employee/employee-payment/add-bill', 'id'=>$model->employee_payment_id])?>',
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){

                if(json.detail){

                    EmployeePayment.update();

                }else{

                    //Importante:
                    //https://github.com/yiisoft/yii2/issues/5991 #7260
                    //TODO: actualizar cdo este disponible
                    for(error in json.errors){

                        $('.field-'+error).addClass('has-error');
                        $('.field-'+error+' .help-block').text(json.errors[error]);

                    }

                }

            });
        }

        this.removeBill = function(element) {
            $elem = $(element);

            if(confirm('<?php echo Yii::t('app', 'You are sure to cancel the bill selection ?') ?>')){
                $.ajax({
                    url: '<?=Url::to(['/employee/employee-payment/delete-bill', 'employee_payment_id'=>$model->employee_payment_id])?>&employee_bill_id='+$elem.val(),
                    dataType: 'json',
                    type: 'post'
                }).done(function(json){
                    if(json.status=='success'){
                        EmployeePayment.update();
                    }else{
                    }
                });
            }
        }

        this.addItem = function() {
            var $form = $("#employee-payment-item");
            var data = $form.serialize();

            $.ajax({
                url: $form.attr('action'),
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){
                if(json.status=='success'){
                    EmployeePayment.update();
                }else if(json.status=='error'){
                    //Importante:
                    //https://github.com/yiisoft/yii2/issues/5991 #7260
                    //TODO: actualizar cdo este disponible
                    for(error in json.errors){
                        $('.field-paymentitem-'+error).addClass('has-error');
                        $('.field-paymentitem-'+error+' .help-block').text(json.errors[error]);
                    }
                }else if(json.status=='error_account'){
                    $('.field-employeepaymentitem-money_box_account_id').addClass('has-error');
                    $('.field-employeepaymentitem-money_box_account_id .help-block').text(json.errors);
                }
            });
        }

        this.removeItem = function(element, event) {
            $elem = $(element);
            var url = $elem.data('url');
            if(confirm($elem.data('confirms'))){
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post'
                }).done(function(json){
                    if(json.status=='success'){
                        EmployeePayment.update();
                    }else{
                    }
                });
            }
        }

        this.update = function() {
            $('#item_amount').val('');
            $.ajax({
                url: '<?=Url::to(['/employee/employee-payment/update', 'id'=>$model->employee_payment_id])?>',
                dataType: 'html',
                type: 'post'
            }).done(function(html){
                $('#new_item').replaceWith(
                    $(html).find('#new_item')
                );
                $('#w_items').replaceWith(
                    $(html).find('#w_items')
                );
                $('#totals').replaceWith(
                    $(html).find('#totals')
                );
                $('#saveButtons').replaceWith(
                    $(html).find('#saveButtons')
                );
                $('#panel_bills').replaceWith(
                    $(html).find('#panel_bills')
                );
            });
        }

        this.changeDate = function(dateText, inst) {
            if(inst.lastVal!="") {
                if(!confirm("<?php echo Yii::t('app', 'Are you sure you want change the date?') ?>")) {
                    $('#employeepayment-date').datepicker( "setDate", inst.lastVal );
                } else {
                    $.ajax({
                        method: 'POST',
                        url: '<?php echo Url::to(['/employee/employee-payment/update-date']) ?>',
                        data: {
                            "employee_payment_id": $("#employee_payment_id").val(),
                            "date": $("#employeepayment-date").val()
                        },
                        dataType: 'json'
                    }).done(function(data){
                        if(data.status!='success') {
                            var date = inst.lastVal;
                            if(data.date) {
                                date = data.date;
                            }
                            $('#employeepayment-date').datepicker( "setDate", date );
                            alert("<?php echo Yii::t('app', 'The date cant be changed.') ?>");
                        }
                    });
                }
            }
        }
    }


</script>
<?php $this->registerJs("EmployeePayment.init();"); ?>