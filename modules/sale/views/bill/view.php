<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\helpers\UserA;
use app\modules\sale\models\DocumentType;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 */

$this->title = $model->typeName.' - ' . Yii::t('app', '#') . ' '.Html::encode($model->number);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$formatter = Yii::$app->formatter;
?>
<div class="bill-view">
    
    <div class="title">
        <h1><?= $this->title ?></h1>
        <p>
            <?php if($model->isEditable): ?>
            <a class="btn btn-primary " href="<?= Url::toRoute(['bill/update','id'=>$model->bill_id]) ?>">
                <span class="glyphicon glyphicon-pencil"></span> <?= Yii::t('app','Update'); ?>
            </a>
            <?php endif; ?>

            <?php if($model->status != 'closed'): ?>
            <a id="closeBill" class="btn btn-danger <?= (!$model->customer_id) ? ' disabled' : '' ?>" href="<?= Url::toRoute(['bill/close','id'=>$model->bill_id]) ?>">
                <span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app','Close'); ?>
            </a>
            <?php endif; ?>

            <?php if(!$model->getDebt(false) == 0 && $model->customer !== null && $model::$payable && Yii::$app->getModule('checkout')): ?>
                <a class="btn btn-success" href="<?= yii\helpers\Url::toRoute(['/checkout/payment/pay-bill', 'bill' => $model->bill_id]) ?>">
                    <span class="glyphicon glyphicon-usd"></span> <?= Yii::t('app', 'Pay') ?>
                </a>
            <?php endif; ?>

            <!-- Se agrega la opción para cargar manualmente el CAE y la Fecha de vencimiento sólo si el comprobante
                esta en cerrado y no los posee. Lo cual significaría que ha sido cargado de forma manual-->
            <?php if($model->status == 'closed' && (!$model->ein) && (!$model->ein_expiration) && ($model->billType->invoice_class_id != null)) {
                echo Html::a("<span class='glyphicon glyphicon-asterisk'></span> " . Yii::t('app', 'Set ein and ein expiration'), ['#'], [
                    'class' => 'btn btn-default',
                    'data-toggle' => 'modal',
                    'data-target' => '#ein-modal',
                    'id' => 'btn-ein-modal'
                ]);
            } ?>

            <!-- Se agrega la opcion de cargar el campo "comprobante hasta", solo a los comprobantes que pertenezcan a un cliente con tipo de documento "venta global diaria".
                Este campo será utilizado para el envio de comprobantes por lotes al sistema S.I.Ap -->
            <?php if($model->customer) {
                if($model->customer->document_type_id == DocumentType::getTypeVentaGlobalDiaria()->document_type_id) {
                    echo Html::a( Yii::t('app', 'Set bill number to'), ['#'], [
                        'class' => 'btn btn-default',
                        'data-toggle' => 'modal',
                        'data-target' => '#bill-number-modal',
                        'id' => 'btn-bill-number-modal'
                    ]);
                }
            } ?>

            <a class="btn btn-default" href="<?= Url::to(['bill/group', 'footprint' => $model->footprint]) ?>"><span class="glyphicon glyphicon-time"></span> <?= Yii::t('app', 'History') ?></a>
        </p>
    </div>
    <h4  class="text-center font-bold">
        <?php if(!$model->active): ?>
            <?= Yii::t('app', 'This bill is not longer active.') ?> 
        <?php endif; ?>
        
    </h4>
    
    <div class="row hidden-print">
        <div class="col-lg-12">

        </div>
    </div>
    <br/>

    <div class="bg-important">
        <div class="row table-view padding-bottom-half">
            <!-- Company y Pto de Venta -->
            <div class="col-sm-2">
                <?= Yii::t('app', 'Company') ?>:
            </div>
            <div class="col-sm-6">
                <?= $model->company ? $model->company->name : null ?>
            </div>
            <div class="col-sm-2">
                <?= Yii::t('app', 'Point of Sale') ?>:
            </div>
            <div class="col-sm-2">
                <?= $model->pointOfSale ? $model->pointOfSale->number : null ?>
            </div>
        </div>

        <!-- Datos Tipo, Emision y demas de factura -->
        <div class="row table-view border padding-top-half padding-bottom-half">
            <div class="col-sm-3">
                <span class="text-center font-s"><?=Yii::t('app','Type')?></span>
                <span class="text-center font-bold"><?= $model->typeName ?></span>

            </div>
            <div class="col-sm-2">
                <span class="text-center font-s"><?=Yii::t('app','Number')?></span>
                <span class="text-center font-bold"><?= $model->status != 'draft' ? $model->number : '' ?></span>
            </div>
            <div class="col-sm-3">
                <span class="text-center font-s"><?=Yii::t('app','Date')?></span>
                <span class="text-center font-bold"><?= $formatter->asDate($model->date);?></span>
            </div>
            <div class="col-sm-2">
                <span class="text-center font-s"><?=Yii::t('app','Time')?></span>
                <span class="text-center font-bold"><?= $formatter->asTime($model->time);?></span>
            </div>
            <div class="col-sm-2">
                <span class="text-center font-s"><?=Yii::t('app','Status')?></span>
                <span class="text-center font-bold"><?= Yii::t('app', ucfirst($model->status)); ?></span>
            </div>
        </div>
        
        <!-- Datos Clientes -->
        <div class="row table-view border padding-top-full padding-bottom-full">
            <div class="col-sm-2">
                <span class=""><?= Yii::t('app', 'Customer') ?>:</span>
            </div>
            <div class="col-sm-10">
                <span class="text-center font-bold">
                    <?php if(!empty($model->customer)){
                        $name = $model->customer->name .' '. $model->customer->lastname;
                        echo UserA::a($name, ['customer/view', 'id'=>$model->customer_id]);
                    }?>
                </span>
            </div>

            <div class="col-sm-2">
                <span class=""><?= Yii::t('app', 'Address') ?>:</span>
            </div>
            <div class="col-sm-10">
                <span class="text-center font-bold  display-block"><?= ($model->customer ? ($model->customer->address ? $model->customer->address->shortAddress: '' ) : '' ) ?></span>
            </div>
            <div class="col-sm-2">
                <span class=""><?= Yii::t('app', 'Observations') ?>:</span>
            </div>
             <div class="col-sm-10 display-block">
                <!-- <hr> -->
                <span class="text-center font-bold  display-block"><?=$model->observation?></span>
            </div>
            <?php if($model::$expirable): ?>
                <div class="col-sm-2">
                    <span class=""><?= Yii::t('app', 'Expiration') ?>:</span>
                </div>
                 <div class="col-sm-10 display-block">
                    <!-- <hr> -->
                    <span class="text-center font-bold  display-block"><?= ucfirst( Yii::$app->formatter->asDate($model->iso_expiration, 'full') ); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if($model->billType->invoiceClass) {  ?>
        <div class="row table-view border ">
            <!-- Datos CAE -->
            <div class="col-sm-1 font-s">
                <?=Yii::t('app','CAE')?>
            </div>
            <div id="ein-div" class="col-sm-5 font-s">
                <?=$model->ein?>
            </div>
            <div class="col-sm-2 font-s">
                <?=Yii::t('app','Vencimiento CAE')?>
            </div>
            <div class="col-sm-4 font-s">
                <?=$formatter->asDate($model->ein_expiration)?>
            </div>
        </div>
        <?php }?>
    </div>


    <div class="row">
        <!-- Status Pago -->
        <?php if($model::$payable): ?>
        
        <div class="col-xs-12">
            
            <?php if($model->getDebt(false)==0 ): ?>
                <div class="alert alert-success font-l text-center font-up margin-top-full margin-bottom-full" role="alert">
                    <?= Yii::t('app','Payed') ?>            
                </div>
            <?php elseif ($model->getPayedAmount()==0): ?>
                <div class="alert alert-danger font-l text-center font-up margin-top-full" role="alert">
                    <?= Yii::t('app','Without payments applied.') ?>
                </div>
                <?php if(!$model->payed && $model->customer !== null && $model::$payable && Yii::$app->getModule('checkout')): ?>
                    <div class="text-center " >
                        <a class="btn btn-success btn-lg" href="<?= Url::toRoute(['/checkout/payment/pay-bill', 'bill' => $model->bill_id]) ?>"><span class="glyphicon glyphicon-usd"></span> <?= Yii::t('app', 'Pay') ?></a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-danger font-l text-center font-up margin-top-full margin-bottom-full" role="alert">
                    <?= Yii::t('app','Pay pending') . " - " . Yii::$app->formatter->asCurrency( $model->getDebt() )  ?>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                        <a class="btn btn-danger btn-sm" style="margin-left: 10px;"
                           href="<?= Url::toRoute(['/checkout/payment/pay-bill', 'bill' => $model->bill_id]) ?>"> <?= Yii::t('app', 'Pay') ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        <?php endif; ?>
        
        <div class="col-lg-12 text-right">
            <a class="btn btn-default" href="<?= Url::to(['bill/group', 'footprint' => $model->footprint]) ?>"><span class="glyphicon glyphicon-time"></span> <?= Yii::t('app', 'History') ?></a>
            <?= $this->render('_generator', ['model' => $model]) ?>
        </div>
        
    </div>

    <?php if($model->billHasPayments): ?>
    
        <!-- Detalle Pago -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="font-s">
                            <?= Yii::t('app','Payment') ?>                        
                        </th>
                        <th class="font-s">
                            <?= Yii::t('app','Payment Method') ?>
                        </th>
                        <th class="font-s">
                            <?= Yii::t('app','Amount') ?>

                        </th>
                        <th class="font-s">
                            <?= Yii::t('app','Ticket Number') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($model->billHasPayments as $payment) {
                    foreach ($payment->payment->paymentItems as $item) { ?>
                        <tr>
                            <td>
                                <?= $payment->payment->payment_id; ?>
                            </td>
                            <td>
                                <?= $item->paymentMethod->name; ?>
                            </td>
                            <td>
                                <?= Yii::$app->formatter->asCurrency($item->amount); ?>
                            </td>
                            <td>
                                <?= $payment->payment->number ?>
                            </td>
                        </tr>
                        <?php
                        }
                }?>
                </tbody>
            </table>
        </div>
   
    <?php endif; ?>

    <div class="row margin-top-full">
        <div class="col-xs-12">
            <h2 class="font-l font-bold font-up">
                <?= Yii::t('app','Detail') ?>

                <?php

                    $view = $model->billType->view;


                    if($model->status == 'completed' && !$model->hasCompletedPayment()){
                        echo UserA::a('<span class="glyphicon glyphicon-repeat"></span> '. Yii::t('app', 'Open {modelClass}', ['modelClass' => Yii::t('app', 'Bill')])

                                , ( empty($url_open) ? Url::toRoute(['/sale/bill/open', 'id' => $model->bill_id]) : $url_open )
                                , ['class' => 'btn btn-info pull-right', 'style' => 'margin-bottom: 10px;']);
                    }
                    
                ?>
            </h2>   


            <?= $this->render('_view', ['form'=>false,'model'=>$model,'detailsProvider'=>$dataProvider]); ?>
        </div>
    </div>

    <!--Modal para actualizar el CAE y la fecha de vencimiento-->
    <?= $this->render('_modal-ein') ?>

    <!-- Modal para ingresar número de comprobante hasta -->
    <?= $this->render('_modal-bill-number-to') ?>

</div>

<script>
    
    //Singleton
    var Bill = new function(){

        this.init = function(){
            console.log('ashjka');

            $("#btn-set-ein").on('click', function (evt) {
                evt.preventDefault();
                if($('#input-ein').val() == '' || $('#input-ein-expiration').val() == '') {
                    alert('Debe completar ambos campos');
                } else {
                    Bill.setEin($('#input-ein').val(), $('#input-ein-expiration').val());
                    $("#ein-modal").modal('hide');
                }
            });

            //Setea el número de comprobante hasta
            $("#btn-set-bill-number").on('click', function (evt) {
                evt.preventDefault();
                if($('#input-bill-number-to').val() == '') {
                    alert('Debe completar el campo');
                } else {
                    Bill.setBillNumberTo($('#input-bill-number-to').val());
                    $("#bill-number-modal").modal('hide');
                }
            });
        }

        this.setBillNumberTo = function (bill_number_to) {
            console.log(bill_number_to);
            $.ajax({
                url: "<?= Url::toRoute('update-bill-number-to') ?>",
                method: 'get',
                dataType: 'json',
                data: {bill_id: <?= $model->bill_id?>, bill_number_to: bill_number_to}
            }).done(function (response) {
                console.log(response);
                // if(response.status == 'success') {
                //     window.location.reload();
                // } else {
                //     alert(response.msg);
                // }
            });
        };

        this.setEin = function (ein, ein_expiration) {
            $.ajax({
                url: "<?= Url::toRoute('update-ein-and-ein-expiration') ?>",
                method: 'get',
                dataType: 'json',
                data: {bill_id: <?= $model->bill_id?>, ein: ein, ein_expiration: ein_expiration}
            }).done(function (response) {
                if(response.status == 'success') {
                    window.location.reload();
                } else {
                    alert(response.msg);
                }
            });
        };

        //public
        this.generate = function(type){
            var data = {
                type: type,
                details: $('#grid').yiiGridView('getSelectedRows')
            }
            window.location.replace(getUrl("<?= \yii\helpers\Url::to(['bill/generate', 'id' => $model->bill_id]) ?>", data));
        }

        function getUrl(url, extraParameters) {
            var extraParametersEncoded = $.param(extraParameters);
            var seperator = url.indexOf('?') == -1 ? "?" : "&";

            return(url + seperator + extraParametersEncoded);
        }

        //public
        this.showAlert = function(type, message, duration){

            var tid = Date.now();
            var alert = '<div id='+tid+' class="alert alert-'+type+' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+message+'</div>';

            $('#alerts').append(alert);

            setTimeout(function(){
                $('#'+tid).hide(500,function(){$('#'+tid).remove()});
            },duration);

        }

    }
    
</script>

<?= $this->registerJs('Bill.init()')?>
