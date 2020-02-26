<?php

use app\modules\config\models\Config;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\companies\CompanySelector;
use app\modules\sale\models\Bill;
use yii\widgets\Pjax;
use app\modules\sale\models\InvoiceProcess;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'Close Pending Batch Invoices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-dismissible" role="alert" id="div-message" style="display: none;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div id="message"></div>
</div>

<div id="messages" style="height: 200px; overflow: auto; display: none; ">
</div>

<div class="batch-invoice">
    <div class="row">
        <div class="col-sm-12">
            <h1><?= Html::encode($this->title) ?></h1>
            <!-- Inicio Seleccion de datos para facturacion -->
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-bill" aria-expanded="true" aria-controls="panel-body-bill">
                    <h3 class="panel-title"><?= Yii::t('app', 'Filter') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-bill" aria-expanded="true">

                    <?php $form = ActiveForm::begin(['id'=>'bill-form', 'method' => 'get']); ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= CompanySelector::widget(['model' => $searchModel, 'id' => 'company_id']); ?>
                        </div>

                        <div class="col-sm-6">
                            <?= $form->field($searchModel, 'bill_type_id')->dropDownList([],[
                                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Bill Type')]),
                                'encode'=>false,
                                'separator'=>'<br/>',
                            ])->label(Yii::t('app','Bill Type')) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?=$form->field($searchModel, 'fromDate')->widget(DatePicker::class, [
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'model' => $searchModel,
                                'attribute' => 'period',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd',
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date')
                                ]
                            ]);
                            ?>
                        </div>

                        <div class="col-sm-6">
                            <?=$form->field($searchModel, 'toDate')->widget(DatePicker::class, [
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'model' => $searchModel,
                                'attribute' => 'period',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd',
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date')
                                ]
                            ]);
                            ?>
                        </div>
                    </div>

                    <?php if(!InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS)) { ?>
                        <div class="row">
                            <div class="col-sm-offset-6 col-sm-3">
                                <div class="form-group field-button">
                                    <label>&nbsp;</label>
                                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-warning form-control', 'id' => 'btnFind' ]) ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group field-button">
                                    <label>&nbsp;</label>
                                    <?= Html::a(Yii::t('app', 'Close'), null, ['class' => 'btn btn-success form-control', 'id'=> 'btnInvoice']) ?>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <h3 id="alert alert-dismissible process-label" class="alert-info"> Procesando ... </h3>
                    <?php } ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div> <!-- Fin Seleccion de datos para facturacion -->

            <!-- Inicio de Progress Bar -->
            <div class="panel panel-default" id="panel-progress">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('app', 'Progress') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-progress" aria-expanded="true">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-6"><?php echo Yii::t('app', 'Total to Process')?></div>
                                <div class="col-sm-6" id="total_to_process"></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6"><?php echo Yii::t('app', 'Processed')?></div>
                                <div class="col-sm-6" id="processed"></div>
                            </div>
                            <div class="row" id="div-without-error" style="display: none;">
                                <div class="col-sm-6"><?php echo Yii::t('app', 'Without error')?></div>
                                <div class="col-sm-6" id="without-error"></div>
                            </div>
                            <div class="row" id="div-with-error" style="display: none;">
                                <div class="col-sm-6"><?php echo Yii::t('app', 'With error')?></div>
                                <div class="col-sm-6" id="with-error"></div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="progress">
                                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin de Progress Bar -->

            <!-- Inicio Seleccion de datos para filtro de facturas -->
            <div class="panel panel-default" id="panel-filtro">
                <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-filter" aria-expanded="true" aria-controls="panel-body-filter">
                    <h3 class="panel-title"><?= Yii::t('app', 'Bills') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-filter" aria-expanded="true">
                    <?php Pjax::begin(
                        [
                            'id' => 'contracts',
                            'enablePushState'=>FALSE
                        ]
                    );
                    if ($dataProvider) {
                        echo GridView::widget([
                            'dataProvider' => $dataProvider,
                            'pjax'=>true,
                            'columns' => [
                                ['class' => 'kartik\grid\SerialColumn'],
                                [
                                    'header'=>Yii::t('app', 'Customer'),
                                    'value' => function($model) {
                                        return $model->customer->lastname .', '.$model->customer->name;
                                    }
                                ],
                                [
                                    'header'=>Yii::t('app', 'Bill'),
                                    'value' => function($model){
                                        $number = $model->status == Bill::STATUS_CLOSED ? ' - '.$model->number : '';
                                        return $model->billType->name . $number;
                                    }
                                ],
                                [
                                    'header'=>Yii::t('app', 'Date'),
                                    'attribute' => 'date',
                                    'format'=>['date']
                                ],
                                [
                                    'header'=>Yii::t('app', 'Amount'),
                                    'attribute'=>'amount',
                                    'format' => ['currency'],
                                ],

                            ],
                        ]);
                    }

                    Pjax::end() ?>
                </div>
            </div> <!-- Fin Seleccion de datos para filtro de facturas -->

        </div>
    </div>
<script>
    var BatchInvoice = new function(){
        this.processing = false;

        this.init = function () {
            $(document).off('change', "#billsearch-company_id").on('change', "#billsearch-company_id", function(){
                BatchInvoice.cargarBillType();
            });
            $(document).off('click', "#btnInvoice").on('click', "#btnInvoice", function(){
                var attr = $('#btnInvoice').attr('disabled');
                if (typeof attr !== typeof undefined && attr !== false) {
                    ev.preventDefault();
                } else {
                    if($('#billsearch-company_id').val() != '' && $('#billsearch-bill_type_id').val() != '') {
                        BatchInvoice.facturar();
                    } else {
                        alert('<?= Yii::t('app', 'Company and bill type cant be empty') ?>');
                    }
                }
            });

            BatchInvoice.cargarBillType();
            $('#panel-progress').hide();
            $('#panel-filtro').show();

            $.ajax({
                url: '<?= Url::to(["invoice-process-close-bill-is-started"])?>',
                method: 'GET',
                datatType: 'json',
                success: function (data) {
                    if(data.invoice_process_started) {
                        $('#panel-progress').show();
                        $('#panel-filtro').hide();
                        BatchInvoice.processing = true;
                        setTimeout(BatchInvoice.getProceso(), 500);
                    } else {
                        BatchInvoice.processing = false;
                    }
                }
            })
        }

        this.cargarBillType = function (){
            $.ajax({
                method: 'POST',
                url: '<?=Url::to(['/sale/batch-invoice/bill-type'])?>',
                data: {
                    'company_id': $( "#billsearch-company_id").val()
                },
                dataType: 'json',
                success: function(data, textStatus, jqXhr) {
                    $("#billsearch-bill_type_id").find("option[value!='']").remove();
                    $.each(data.output, function (i, item) {
                        $("#billsearch-bill_type_id").append($('<option>', {
                            value: item.value,
                            text : item.text
                        }));
                    });
                    $("#billsearch-bill_type_id").removeAttr('disabled');
                    $("#billsearch-bill_type_id").val(<?=$searchModel->bill_type_id?>);
                }
            });
        }

        this.getPostData = function () {
            var postdata = {
                'BillSearch[company_id]': $('#billsearch-company_id').val(),
                'BillSearch[bill_type_id]': $('#billsearch-bill_type_id').val(),
                'BillSearch[fromDate]' : $('#billsearch-fromdate').val(),
                'BillSearch[toDate]' : $('#billsearch-todate').val(),
            };
            try {
                var date = $('#billsearch-period').kvDatepicker('getDate');
                date =  "01-" +  (date.getMonth() + 1) + "-" + date.getFullYear();
                postdata['BillSearch[period]'] = date;
            } catch(e){}

            return postdata;
        }

        this.facturar = function() {
            if(!BatchInvoice.processing) {
                BatchInvoice.processing = true;
                if (confirm('<?=Yii::t('app', 'You are sure to bill all contracts listed ?')?>')) {
                    $('#btnInvoice').attr('disabled', 'disabled');
                    $('#btnInvoice').html('Procesando ...');
                    $("#div-without-error").hide();
                    $("#div-with-error").hide();
                    $("#messages").hide();
                    $('#panel-progress').show();
                    $('#panel-filtro').hide();

                    var postdata = BatchInvoice.getPostData();
                    setTimeout(function () {
                        setTimeout(BatchInvoice.getProceso(), 1000);

                        $.ajax({
                            method: 'POST',
                            url: '<?=Url::to(['/sale/batch-invoice/close-invoices'])?>',
                            data: postdata,
                            dataType: 'json',
                            success: function (data, textStatus, jqXhr) {
                                console.log(data);
                                if (data.status == "success") {
                                    $("#div-message").addClass('alert-info');
                                    $("#div-message").find('#message').html(data.message);
                                    $("#div-message").show();
                                } else {
                                    $("#div-message").addClass('alert-danger');
                                    $("#div-message").find('#message').html(data.message);
                                    $("#div-message").show();
                                }
                            }
                        });
                    }, 500);
                }
            }
            return false;
        }

        this.getProceso = function() {
            setTimeout(function(){
                $.ajax({
                    method: 'POST',
                    url: '<?=Url::to(['/sale/batch-invoice/get-process'])?>',
                    dataType: 'json',
                    data: {
                        'process': '_invoice_close_process_'
                    },
                    success: function(data, textStatus, jqXhr) {
                        var value = ((data.qty*100)/data.total);
                        $('.progress-bar').css('width', value+'%').attr('aria-valuenow', value);

                        $('#total_to_process').html(data.total);
                        $('#processed').html(data.qty);
                        if(data.total!=data.qty) {
                            $('.progress-bar').html(parseInt( value) +'%');
                        } else {
                            $('.progress-bar').html('<?= Yii::t('app', 'Process finished') ?>');
                            $('#process-label').addClass('hidden');
                            if(data.total != 0 && data.qty != 0) {
                                BatchInvoice.processing = false;
                            }
                        }

                        if(data.errors.length > 0) {
                            var string = '';
                             errores = data.errors.length;
                             for (i in data.errors){
                                 string = string + data.errors[i] + "<br>";
                             };

                            $("#div-message").addClass('alert-danger');
                            $("#div-message").find('#message').html(string);
                            $("#div-message").show();
                        }

                        if( BatchInvoice.processing ) {
                            BatchInvoice.getProceso();
                        }
                    }
                });
            }, 2000)
        }
    }
</script>
<?php $this->registerJs('BatchInvoice.init()');?>