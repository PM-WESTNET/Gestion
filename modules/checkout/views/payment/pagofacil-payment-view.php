<?php

use app\modules\checkout\models\PagoFacilTransmitionFile;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model app\modules\checkout\models\PagoFacilTransmitionFile
 */
$this->title = 'Pago Fácil - ' . $model->upload_date . ' - ' . $model->file_name;
$this->params['breadcrumbs'][]= ['label' => "Archivo de Pago Fácil", 'url' => ['pagofacil-payments-index']];
$this->params['breadcrumbs'][] = 'Pago Fácil - ' . $model->upload_date;
?>

<div class="title">
    <div class="messages"></div>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($model->status == 'draft'): ?>
        <p>
        <?= Html::a(Yii::t('westnet','Confirm and process file'), '#', [
            'class' => 'btn btn-success', 
            'id' => 'confirm',
            ]) ?>
        </p>
    <?php endif; ?>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'upload_date',
            /**[
                'label' => 'Account',
                'value' => function (PagoFacilTransmitionFile $model) {
                    $account = MoneyBoxAccount::findOne(['money_box_account_id' => $model->money_box_account_id]);
                    error_log(print_r($account),1);
                    return $account->account->name . ' - ' . $account->moneyBox->name;
                },
            ],**/
            [
                'attribute' => 'total',
                'format' => 'currency',
            ],
            'file_name',
        ],       
      ]);
    ?>

    <!-- Inicio de Progress Bar -->
    <?php if ($model->status == PagoFacilTransmitionFile::STATUS_PENDING) { ?>
        <div class="panel panel-default" id="panel-progress">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'Progress') ?></h3>
            </div>
            <div class="panel-body collapse in" id="panel-body-progress" aria-expanded="true">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-6"><?php echo Yii::t('app', 'Total to Process') ?></div>
                            <div class="col-sm-6" id="total_to_process"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6"><?php echo Yii::t('app', 'Processed') ?></div>
                            <div class="col-sm-6" id="processed"></div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="progress">
                            <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0"
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- Fin de Progress Bar -->

    <?php
        $columns[] = ['class' => 'yii\grid\SerialColumn'];
    //Columna de empresa, solo si se encuentra activa la func. de empresas
    /**if(Yii::$app->params['companies']['enabled']){
        $columns[] = [
            'value' => function($model) {
                return $model->company_name;
            },
            'label' => Yii::t('app', 'Company')
        ];
    }**/

    $columns = array_merge($columns, [
        [
            'label' => Yii::t('app', 'Customer Number'),
            'value' => 'paymentPayment.customer.code'
        ],
        [
            'header' => Yii::t('app','Customer'),
            'attribute' => function($model){ return $model->paymentPayment->customer ? Html::a($model->paymentPayment->customer->fullName, ['/sale/customer/view', 'id'=>$model->paymentPayment->customer_id]) : null; },
            'format' => 'raw'
        ],
        'paymentPayment.date:date',
        [
            'attribute' => 'paymentPayment.amount',
            'format' => ['currency'],
        ],        
        [
            'label' => Yii::t('app', 'Status'),
            'value' => function($model) {
                return Yii::t('app', ucfirst($model->paymentPayment->status));
            },
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{view} {update} {delete} {pdf}',
            'buttons'=>[
                'view' => function ($url, $model, $key){
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['payment/view', 'id'=>$model->paymentPayment->payment_id]), ['class' => 'btn btn-view']);
                },
                'pdf' => function ($url, $model, $key) {
                    return ($model->paymentPayment->status == 'closed' ?
                        Html::a('<span class="glyphicon glyphicon-print"></span>', Url::toRoute(['payment/pdf', 'id'=>$model->paymentPayment->payment_id]), ['target'=>"_blank", 'class' => 'btn btn-print']) : '') ;
                },
                'delete' => function ($url, $model, $key) {
                    if($model->paymentPayment->status === 'draft'){
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['payment/delete', 'id'=>$model->paymentPayment->payment_id]), [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '1',
                            'class' => 'btn btn-danger'
                        ]);
                    }
                },
                'update' => function ($url, $model, $key) {
                    return $model->paymentPayment->status == 'draft' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['payment/update', 'id'=>$model->paymentPayment->payment_id]), ['class' => 'btn btn-primary']) : '';
                },
            ]
        ]
    ]);

    echo GridView::widget([
        'dataProvider' => $payments,
        'columns' => $columns,
    ]); ?>        
</div>

<script>

    var PagoFacilView= new function(){
        this.processing = false;

        this.init= function(){
           $(document).on('click', '#confirm', function(e){
                e.preventDefault();
                $('#confirm').html('Procesando...');
                PagoFacilView.confirm();
           });

           PagoFacilView.checkIfProcessItsStarted();
        
        }

        this.checkIfProcessItsStarted = function () {
            $.ajax({
                url: '<?= Url::to(["close-payment-process-started"])?>',
                method: 'GET',
                data: {pago_facil_transmition_file_id : <?= $model->pago_facil_transmition_file_id?> },
                datatType: 'json',
                success: function (data) {
                    console.log(data);
                    if(data.process_started) {
                        console.log('started');
                        $('#panel-progress').show();
                        $('#panel-filtro').hide();
                        PagoFacilView.processing = true;
                        setTimeout(PagoFacilView.getProceso(), 500);
                    } else {
                        console.log('no started');
                        PagoFacilView.processing = false;
                    }
                }
            })
        }

        this.getProceso = function() {
            setTimeout(function(){
                $.ajax({
                    method: 'POST',
                    url: '<?=Url::to(['get-close-payment-process'])?>',
                    dataType: 'json',
                    success: function(data, textStatus, jqXhr) {
                        console.log(data);
                        var value = ((data.qty*100)/data.total);
                        $('.progress-bar').css('width', value+'%').attr('aria-valuenow', value);
                        $('#total_to_process').html(data.total);
                        $('#processed').html(data.qty);
                        if(data.total!=data.qty) {
                            $('.progress-bar').html(parseInt( value) +'%');
                        } else {
                            $('.progress-bar').html('<?php echo Yii::t('app', 'Process finished') ?>');
                            $('#process-label').addClass('hidden');
                            if(data.total != 0 && data.qty != 0) {
                                PagoFacilView.processing = false;
                            }
                        }

                        if( PagoFacilView.processing ) {
                            PagoFacilView.getProceso();
                        }
                    }
                });
            }, 2000)
        }
        
        this.confirm= function(){
            $.ajax({
                url: '<?= Url::to(['payment/confirm-file'])?>&idFile=<?=$model->pago_facil_transmition_file_id?>',
                method: 'POST',
                dataType: 'json'
            }).done(function(data) {
                window.location.reload();
            }).fail(function(jqXHR){
                $('#confirm').html('<?php echo Yii::t('westnet','Confirm and process file')?>');
                $('.messages').html('<div class="alert alert-danger">Ocurrió un error en el server. ' + jqXHR.text + '</div>')
            });
        }
    }



</script>
<?php $this->registerJs('PagoFacilView.init()')?>

