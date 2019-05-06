<?php

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
        this.init= function(){
           $(document).on('click', '#confirm', function(e){
                e.preventDefault();
                $('#confirm').html('Procesando...');
                PagoFacilView.confirm();
           }); 
        
        }
        
        this.confirm= function(){
            $.ajax({
                url: '<?= Url::to(['payment/confirm-file'])?>&idFile=<?=$model->pago_facil_transmition_file_id?>',
                method: 'POST',
                dataType: 'json',
                success: function(data){
                    window.location.reload();                
                }               
                
            });
        }
    }



</script>
<?php $this->registerJs('PagoFacilView.init()')?>

