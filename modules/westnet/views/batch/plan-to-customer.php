<?php

use app\modules\config\models\Config;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('westnet', 'Batch Process - Assign plans to customer');
$this->params['breadcrumbs'][] = Yii::t('westnet', 'Batch Process - Assign plans to customer');
?>
<div class="alert alert-dismissible" role="alert" id="div-message" style="display: none;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div id="message"></div>
</div>

<div id="messages" style="height: 200px; overflow: auto; display: none; ">
</div>

<div class="batch-plans">
    <div class="row">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php $form = ActiveForm::begin(['id'=>'plan-form', 'method' => 'get']); ?>
            <!-- Inicio Seleccion de datos para facturacion -->
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-plan" aria-expanded="true" aria-controls="panel-body-plan">
                    <h3 class="panel-title"><?= Yii::t('app', 'Filter') ?></h3>
                </div>
                <div class="panel-body collapse in" id="panel-body-plan" aria-expanded="true">

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($searchModel, 'product_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map(\app\modules\sale\modules\contract\models\Plan::find()->where(['type'=>'plan'])->orderBy(['name'=>SORT_ASC])->all(), 'product_id', 'name'),[
                                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Plan')]),
                                'encode'=>false,
                                'separator'=>'<br/>',
                            ]) ?>
                        </div>

                        <div class="col-sm-6">
                            <?= $form->field($searchModel, 'customer_category_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map(\app\modules\sale\models\CustomerCategory::find()->all(), 'customer_category_id', 'name'),[
                                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Customer Category')]),
                                'encode'=>false,
                                'separator'=>'<br/>',
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($searchModel, 'server_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map(\app\modules\westnet\models\Server::find()->all(), 'server_id', 'name'),[
                                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Server')]),
                                'encode'=>false,
                                'separator'=>'<br/>',
                            ]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($searchModel, 'node_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map(\app\modules\westnet\models\Node::find()->all(), 'node_id', 'name'),[
                                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Node')]),
                                'encode'=>false,
                                'separator'=>'<br/>',
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group field-button">
                                <label>&nbsp;</label>
                                <?= Html::submitButton(Yii::t('westnet', 'Find Customers'), ['class' => 'btn btn-warning form-control', 'id' => 'btnFind' ]) ?>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($searchModel, 'new_product_id')->dropDownList(
                                \yii\helpers\ArrayHelper::map(\app\modules\sale\modules\contract\models\Plan::find()->where(['type'=>'plan'])->orderBy(['name'=>SORT_ASC])->all(), 'product_id', 'name'),[
                                'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app','Plan')]),
                                'encode'=>false,
                                'separator'=>'<br/>',
                            ])->label(Yii::t('westnet','New Plan')) ?>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group field-button">
                                <label>&nbsp;</label>
                                <?= Html::a(Yii::t('westnet', 'Assign'), null, ['class' => 'btn btn-success form-control', 'id'=> 'btnAssign']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- Fin Seleccion de datos para facturacion -->

            <?php ActiveForm::end(); ?>
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
                    <h3 class="panel-title"><?= Yii::t('westnet', 'Customers to Change') ?></h3>
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
                                    'attribute'=>'customer',

                                ],
                                [
                                    'header'=>Yii::t('app', 'Description'),
                                    'attribute'=>'plan',
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
    var BatchPlanToCustomer = new function(){
        this.processing = false;

        this.init = function () {
            $(document).off('click', "#btnAssign").on('click', "#btnAssign", function(){
                BatchPlanToCustomer.assign();
            });

            $('#panel-progress').hide();
            $('#panel-filtro').show();
        }


        this.getPostData = function () {
            var postdata = {
                'CustomerContractSearch[product_id]': $('#customercontractsearch-product_id').val(),
                'CustomerContractSearch[customer_category_id]': $('#customercontractsearch-customer_category_id').val(),
                'CustomerContractSearch[server_id]': $('#customercontractsearch-server_id').val(),
                'CustomerContractSearch[node_id]': $('#customercontractsearch-node_id').val(),
                'CustomerContractSearch[new_product_id]': $('#customercontractsearch-new_product_id').val(),
            };
            return postdata;
        }

        this.assign = function() {
            if(!BatchPlanToCustomer.processing) {
                if($('#customercontractsearch-new_product_id').val()==0){
                    return;
                }
                BatchPlanToCustomer.processing = true;
                if (confirm('<?=Yii::t('westnet', 'You are sure to assign the plan to all the customers selected ?')?>')) {
                    $("#div-without-error").hide();
                    $("#div-with-error").hide();
                    $("#messages").hide();
                    $('#panel-progress').show();
                    $('#panel-filtro').hide();

                    var postdata = BatchPlanToCustomer.getPostData();
                    setTimeout(function () {
                        setTimeout(BatchPlanToCustomer.getProceso(), 1000);
                        $.ajax({
                            method: 'POST',
                            url: '<?=Url::to(['/westnet/batch/plan-to-customer-assign'])?>',
                            data: postdata,
                            dataType: 'json',
                            success: function (data, textStatus, jqXhr) {
                                if (data.status == 'success') {
                                    BatchPlanToCustomer.processing = false;
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
                                    } else {
                                        $('.progress-bar').css('width', data.total+'%').attr('aria-valuenow', data.total);
                                        $('.progress-bar').html('<?php echo Yii::t('app', 'Process finished') ?>');
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
                BatchPlanToCustomer.processing = false;
            }
            return false;
        }

        this.getProceso = function() {
            setTimeout(function(){
                $.ajax({
                    method: 'POST',
                    url: '<?=Url::to(['/westnet/batch/get-process'])?>',
                    data: {
                        'process': '_batch_to_customer_'
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
                        if( BatchPlanToCustomer.processing ) {
                            BatchPlanToCustomer.getProceso();
                        }
                    }
                });
            }, 1000)
        }
    }
</script>
<?php $this->registerJs('BatchPlanToCustomer.init()');?>