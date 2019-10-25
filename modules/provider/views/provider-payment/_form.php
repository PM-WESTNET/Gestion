<?php

use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\provider\models\Provider;
use app\modules\provider\models\ProviderBillHasProviderPayment;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use \app\modules\provider\models\ProviderPaymentItem;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\ProviderPayment */
/* @var $form yii\widgets\ActiveForm */

$payment_method_bank = Config::getValue('payment_method_bank');;
$payment_method_paycheck = Config::getValue('payment_method_paycheck');
$payment_method_cash = Config::getValue('payment_method_cash');

?>

<div class="provider-payment-form">

    <?php
        $form = ActiveForm::begin(['id'=>'provider-payment-form']);
        echo Html::hiddenInput('ProviderPayment[provider_payment_id]', $model->provider_payment_id, ['id'=>'provider_payment_id']);
    ?>
    
    <?= Html::hiddenInput('ProviderPayment[status]', $model->status, ['id'=>'payment_status']) ?>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?php
    echo $this->render('@app/modules/partner/views/partner-distribution-model/_selector', ['model' => $model, 'form'=>$form]);
    ?>

    
    <div class="form-group<?= $model->hasErrors('provider_id') ? ' has-error' : '' ?>">
        <?php
        if (!$model->provider) {
            ?>
            <div class="input-group" style="z-index:0;">
                <?= Html::label(Yii::t('app', "Provider"), ['provider_id']) ?>
                <?=Select2::widget([
                    'model' => $model,
                    'attribute' => 'provider_id',
                    'data' => yii\helpers\ArrayHelper::map(Provider::find()->all(), 'provider_id', 'name' ),
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                echo Html::error($model, 'provider_id', ['class' => 'help-block']);
                ?>
            </div>
        <?php } else {
            echo Html::activeLabel($model, 'provider') ;
            echo '<div class="form-control filter dates hasDatepicker">'.$model->provider->name.'</div>';
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
                    'onSelect' => new \yii\web\JsExpression('function(dateText, inst) { return ProviderPayment.changeDate(dateText, inst) }')
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
                echo $this->render('_payment-items', ['model'=>$model, 'item'=>new ProviderPaymentItem()]);
                // Listado de detalles
                \yii\widgets\Pjax::begin(['id'=>'w_items']);

                $dataProvider = new ActiveDataProvider([
                    'query' => $model->getProviderPaymentItems()
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
                                                'data-url' => yii\helpers\Url::toRoute(['provider-payment/delete-item', 'provider_payment_item_id'=>$key]),
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
            echo $this->render('_form-bills', ['model'=>$model,'dataProvider'=>$billDataProvider, 'pbp'=>new ProviderBillHasProviderPayment()]);
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
            echo Html::a(Yii::t('app', 'Save draft'),['view' , 'id' => $model->provider_payment_id], [
                'class' => 'btn btn-warning',
            ]);
        }?>
    </div>
    <input type="hidden" id="provider_bill_amount" value="<?php echo $model->calculateTotalPayed()?>" />
    <input type="hidden" id="amount_total" value="<?php echo $model->amount?>" />
</div>
<script>
    var ProviderPayment = new function() {

        this.init = function() {

            $(document).on("click", "#bill-add", function(){
                ProviderPayment.addBill();
            });

            $(document).off('click', '#item-add')
                .on('click', '#item-add', function(){
                    ProviderPayment.addItem();
                });
            $(document).off('click', '.payment-item-delete')
                .on('click', '.payment-item-delete', function(){
                    ProviderPayment.removeItem(this);
                });

            $(document).on("keyup", "#provider_bill_amount", function(){
                ProviderPayment.validateAmount();
            });

            $(document).off("click", "#closePayment")
                .on("click", "#closePayment", function(){
                ProviderPayment.close();
            });
            $(document).off("click", "#savePayment")
                .on("click", "#savePayment", function(){
                ProviderPayment.save();
            });

            $(document).off('click', '.checkbill')
                .on('click', '.checkbill', function(){
                var input = $(this).closest('tr').find('.total_amount');
                if($(this).is(':checked')) {
                    input.removeAttr('disabled');
                    input.val(input.data('max'));
                    ProviderPayment.addBill(this);
                } else {
                    input.attr('disabled', true);
                    input.val(0);
                    ProviderPayment.removeBill(this);
                }
            });

            $(document).off('blur', '.total_amount')
                .on('blur', '.total_amount', function(event){
                    event.stopPropagation();
                    ProviderPayment.updateAmount(this);
            });

            $(document).off('keypress', '.total_amount')
                .on('keypress', '.total_amount', function(e){
                    e.stopPropagation();
                    if((e.keyCode || e.which) == 13) {
                        ProviderPayment.updateAmount(this);
                    }

                });

            $("#payment_method_id").trigger("change");


            <?php if (Yii::$app->getModule('paycheck') && $model->provider_payment_id ) { ?>
                SearchPaycheck.onSelect = function (json){
                    $("#providerpayment-amount").val(json.paycheck.amount);
                }
            <?php } ?>

            $(document).off('change', '#providerpayment-date').on('change', '#providerpayment-date', function(){
                $('.ui-datepicker-current-day').click();
            });
        }

        this.save = function() {
            $("#provider-payment-form").submit();
        }

        this.close = function() {
            if (confirm('<?=Yii::t('app', 'Are you sure you want to close the Payment?' )?>')) {
                $("#provider-payment-form").attr('action', "<?=Url::to(['/provider/provider-payment/close', 'id'=>$model->provider_payment_id])?>" );
                $("#provider-payment-form").submit();
            }
        }

        this.validateAmount = function() {
            var billAmount  = parseFloat($("#provider_bill_amount").val());
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
                    ProviderPayment.addBill($(elem).closest('tr').find('.checkbill'));
                }
            }
        }


        this.addBill = function(elem) {
            var data = {
                'ProviderBillHasProviderPayment': {
                    'provider_bill_id': $(elem).val(),
                    'provider_payment_id': <?php echo ($model->provider_payment_id ? $model->provider_payment_id: "''" )  ?>,
                    'amount': $(elem).closest('tr').find('.total_amount').val()
                }
            };

            $.ajax({
                url: '<?=Url::to(['/provider/provider-payment/add-bill', 'id'=>$model->provider_payment_id])?>',
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){

                if(json.detail){

                    ProviderPayment.update();

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
                    url: '<?=Url::to(['/provider/provider-payment/delete-bill', 'provider_payment_id'=>$model->provider_payment_id])?>&provider_bill_id='+$elem.val(),
                    dataType: 'json',
                    type: 'post'
                }).done(function(json){
                    if(json.status=='success'){
                        ProviderPayment.update();
                    }else{
                    }
                });
            }
        }

        this.addItem = function() {
            var $form = $("#provider-payment-item");
            var data = $form.serialize();

            $.ajax({
                url: $form.attr('action'),
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){
                if(json.status=='success'){
                    ProviderPayment.update();
                }else if(json.status=='error'){
                    //Importante:
                    //https://github.com/yiisoft/yii2/issues/5991 #7260
                    //TODO: actualizar cdo este disponible
                    for(error in json.errors){
                        $('.field-paymentitem-'+error).addClass('has-error');
                        $('.field-paymentitem-'+error+' .help-block').text(json.errors[error]);
                    }
                }else if(json.status=='error_account'){
                    $('.field-providerpaymentitem-money_box_account_id').addClass('has-error');
                    $('.field-providerpaymentitem-money_box_account_id .help-block').text(json.errors);
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
                        ProviderPayment.update();
                    }else{
                    }
                });
            }
        }

        this.update = function() {
            $.ajax({
                url: '<?=Url::to(['/provider/provider-payment/update', 'id'=>$model->provider_payment_id])?>',
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
                    $('#providerpayment-date').datepicker( "setDate", inst.lastVal );
                } else {
                    $.ajax({
                        method: 'POST',
                        url: '<?php echo Url::to(['/provider/provider-payment/update-date']) ?>',
                        data: {
                            "provider_payment_id": $("#provider_payment_id").val(),
                            "date": $("#providerpayment-date").val()
                        },
                        dataType: 'json'
                    }).done(function(data){
                        if(data.status!='success') {
                            var date = inst.lastVal;
                            if(data.date) {
                                date = data.date;
                            }
                            $('#providerpayment-date').datepicker( "setDate", date );
                            alert("<?php echo Yii::t('app', 'The date cant be changed.') ?>");
                        }
                    });
                }
            }
        }
    }


</script>
<?php $this->registerJs("ProviderPayment.init();"); ?>