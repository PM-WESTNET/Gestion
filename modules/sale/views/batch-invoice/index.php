<?php

use app\modules\config\models\Config;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\companies\CompanySelector;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'Batch Invoice');
$this->params['breadcrumbs'][] = Yii::t('app', 'Batch Invoice');
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
                    <h3 class="panel-title"><?= Yii::t('app', 'Invoice Data') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-bill" aria-expanded="true">

                    <?php $form = ActiveForm::begin(['id'=>'bill-form', 'method' => 'get']); ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= CompanySelector::widget(['model'=>$searchModel, 'id'=>'company_id', 'conditions'=>['parent_id' => new \yii\db\Expression('parent_id is not null')]]); ?>
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
                            <?=$form->field($searchModel, 'period')->widget(DatePicker::classname(), [
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'model' => $searchModel,
                                'attribute' => 'period',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-mm-yyyy',
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date')
                                ]
                            ]);
                            ?>
                        </div>
                        <div class="col-sm-12">
                            <label> <?= Yii::t('app', 'Informative message')?></label>
                            <input class="form-control" id="bill-observation">
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group field-button">
                                <label>&nbsp;</label>
                                <?= Html::submitButton(Yii::t('app', 'Find Contracts'), ['class' => 'btn btn-warning form-control', 'id' => 'btnFind', 'data-loading-text' =>  Yii::t('app', 'Processing') ]) ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group field-button">
                                <label>&nbsp;</label>
                                <?= Html::a(Yii::t('app', 'Is Invoiced'), null, ['class' => 'btn btn-success form-control', 'id'=> 'btnInvoice', 'data-loading-text' =>  Yii::t('app', 'Processing')]) ?>
                            </div>
                        </div>
                    </div>

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
                    <h3 class="panel-title"><?= Yii::t('app', 'Contract to Invoice') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-filter" aria-expanded="true">
                    <?php
                    \yii\widgets\Pjax::begin(
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
                                    'format' => 'raw',
                                    'value'=>function($model){
                                        return Html::a($model['code'] . ' - ' . $model['customer'], ['/sale/customer/view', 'id' => $model['customer_id']]);
                                    },

                                ],
                                [
                                    'header'=>Yii::t('app', 'Contracts'),
                                    'attribute' => 'contracts',
                                ],
                            ],
                        ]);
                    }

                    \yii\widgets\Pjax::end() ?>
                </div>
            </div> <!-- Fin Seleccion de datos para filtro de facturas -->

        </div>
    </div>
<script>
    var BatchInvoice = new function(){
        this.processing = false;

        this.init = function () {
            $(document).off('change', "#contractsearch-company_id").on('change', "#contractsearch-company_id", function(){
                BatchInvoice.cargarBillType();
            });
            $(document).off('click', "#btnInvoice").on('click', "#btnInvoice", function(){
                BatchInvoice.facturar();
            });

            BatchInvoice.cargarBillType();
            $('#panel-progress').hide();
            $('#panel-filtro').show();
        }

        this.cargarBillType = function (){
            $.ajax({
                method: 'POST',
                url: '<?=Url::to(['/sale/batch-invoice/bill-type'])?>',
                data: {
                    'company_id': $( "#contractsearch-company_id").val()
                },
                dataType: 'json',
                success: function(data, textStatus, jqXhr) {
                    $("#contractsearch-bill_type_id").find("option[value!='']").remove();
                    $.each(data.output, function (i, item) {
                        $("#contractsearch-bill_type_id").append($('<option>', {
                            value: item.value,
                            text : item.text
                        }));
                    });
                    $("#contractsearch-bill_type_id").removeAttr('disabled');
                    $("#contractsearch-bill_type_id").val(<?=$searchModel->bill_type_id?>);
                }
            });
        }

        this.getPostData = function () {
            var postdata = {
                'ContractSearch[company_id]': $('#contractsearch-company_id').val(),
                'ContractSearch[bill_type_id]': $('#contractsearch-bill_type_id').val(),
            };
            try {
                var date = $('#contractsearch-period').kvDatepicker('getDate');
                date =  "01-" +  (date.getMonth() + 1) + "-" + date.getFullYear();
                postdata['ContractSearch[period]'] = date;
                postdata['bill_observation'] = $('#bill-observation').val();
            } catch(e){}

            return postdata;
        }

        this.facturar = function() {
            if(!BatchInvoice.processing) {
                BatchInvoice.processing = true;
                if (confirm('<?=Yii::t('app', 'You are sure to bill all contracts listed ?')?>')) {
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
                            url: '<?=Url::to(['/sale/batch-invoice/invoice'])?>',
                            data: postdata,
                            dataType: 'json',
                            success: function (data, textStatus, jqXhr) {
                                if (data.status == 'success') {
                                    BatchInvoice.processing = false;
                                    var errores = 0;
                                    var exitosos = 0;
                                    if(data.messages.error) {
                                        errores = data.messages.error.length;
                                        for (i in data.messages.error){
                                            var div = $("#div-message").clone();
                                            div.addClass('alert-danger');
                                            div.find('#message').html(data.messages.error[i]);
                                            div.show();
                                            div.appendTo("#messages");

                                        };
                                        $("#messages").show();

                                        if(data.messages.success) {
                                            exitosos = data.messages.success.length;
                                        }
                                        $("#without-error").html(exitosos);
                                        $("#with-error").html(errores);
                                        $("#div-without-error").show();
                                        $("#div-with-error").show();
                                    }

                                } else {
                                    for (error in data.errors) {

                                        $('.field-' + error).addClass('has-error');
                                        $('.field-' + error + ' .help-block').text(data.errors[error]);

                                    }
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
                    data: {
                        'process': '_invoice_all_'
                    },
                    dataType: 'json',
                    success: function(data, textStatus, jqXhr) {
                        var value = ((data.qty*100)/data.total);
                        $('.progress-bar').css('width', value+'%').attr('aria-valuenow', value);

                        $('#total_to_process').html(data.total);
                        $('#processed').html(data.qty);
                        if(data.total!=data.qty) {
                            $('.progress-bar').html(parseInt( value) +'%');
                        } else {
                            $('.progress-bar').html('<?php echo Yii::t('app', 'Process finished') ?>');
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