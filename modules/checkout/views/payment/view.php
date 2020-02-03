<?php

use app\modules\checkout\models\Payment;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Payment */

$this->title = Yii::t('app','Payment').' '.$model->payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
            if ($model->getUpdatable()) {
                echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " .Yii::t('app', 'Update'), ['update', 'id' => $model->payment_id], ['class' => 'btn btn-primary']);
            }
            if ($model->status !== 'draft'){
                echo Html::a("<span class='glyphicon glyphicon-indent-right'></span> " .Yii::t('app', 'Apply to Bill'), ['apply', 'id' => $model->payment_id], ['class' => 'btn btn-warning']);
            }
            if ($model->canClose()) {
                echo Html::a("<span class='glyphicon glyphicon-repeat'></span> " .Yii::t('app', 'Close'), ['close', 'payment_id' => $model->payment_id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to close the Payment?'),
                        'method' => 'post',
                    ],
                ]);
            }

            if($model->deletable){
                echo Html::a("<span class='glyphicon glyphicon-remove'></span> " .Yii::t('app', 'Delete'), ['delete', 'id' => $model->payment_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }

            if($model->status === Payment::PAYMENT_CLOSED && ($model->customer ? trim($model->customer->email) : "" ) !=""){
                echo Html::a('<span class="glyphicon glyphicon-envelope"></span> '. Yii::t('app', 'Send By Email'), Url::toRoute(['email', 'id' => $model->payment_id, 'from' => 'index']), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);
            }
                echo Html::a("<span class='glyphicon glyphicon-user'></span> " .Yii::t('app', 'Change Customer'), '#', ['class' => 'btn btn-warning', 'id' => 'change-customer']);
            ?>
        </p>
    </div>

        <?php

        $attributes = [];
        if (Yii::$app->params['companies']['enabled']) {
            $attributes[] = [
                'label' => Yii::t('app', 'Company'),
                'value' => $model->company_id ? $model->company->name: ''
            ];
        }
        $attributes = array_merge($attributes, [
            [
                'attribute' => 'customer',
                'value' => function($model) {
                    if(!$model->customer_id) {
                        return '';
                    }
                    return Html::a($model->customer->fullName, ['/sale/customer/view', 'id' => $model->customer_id]);
                },
                'format' => 'raw'
            ],
            'date:date',
            [
                'attribute' => 'timestamp',
                'value' => function($model) {
                    return $model->timestamp ? (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d-m-Y') : '';
                }
            ],
            'number',
            [
                'attribute' => 'status',
                'value' => Yii::t('app', ucfirst($model->status))
            ],
            'amount:currency',
            'balance:currency'
        ]);
        
        if ($model->ecoPago !== null) {
            $ecoPago= [
                'label'=> 'Realizado en Ecopago',
                'value' => $model->ecoPago->name,
            ];
            
            array_push($attributes, $ecoPago);
        }

        echo DetailView::widget([
            'model' => $model,
            'attributes' => $attributes]); ?>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Detail') ?></h3>
        </div>
        <div class="panel-body">
            <?php
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getPaymentItems(),
            ]);

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
                        'attribute' => 'user_id',
                        'value' => function ($model){
                           if ($model->user) {
                               return $model->user->username;
                           }
                        }
                    ],
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Bills') ?></h3>
        </div>
        <div class="panel-body">
            <?php
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getBillHasPayments(),
            ]);

            echo GridView::widget([
                'id'=>'grid',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('app', 'Bill'),
                        'value' => function($model) {
                            return ($model->bill ? ($model->bill->billType ? $model->bill->billType->name . " - " : "") . $model->bill->number : "");
                        }
                    ],
                    'bill.date',
                    [
                        'attribute' => 'bill.total',
                        'label'     => Yii::t('app', 'Total'),
                        'format'    => ['currency']
                    ],
                    [
                        'attribute' => 'amount',
                        'label'     => Yii::t('app', 'Amount applied'),
                        'format'    => ['currency']
                    ],
                    [
                        'attribute' => 'bill.debt',
                        'label'     => Yii::t('app', 'Balance'),
                        'format'    => ['currency']
                    ],
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>
</div>
<div id="customer-modal" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=Yii::t('app', 'Change Customer')?></h4>
      </div>
      <div class="modal-body">
          <?php
            $form = ActiveForm::begin();
            
            echo $this->render('../../../sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => new Payment(), 'attribute' => 'customer_id']);
            
            ActiveForm::end();
          ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="change-customer-confirm"><?=Yii::t('app', 'Change Customer')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    var PaymentView = new function(){
        this.init = function() {
            $($(".glyphicon-print").parent()).each(function(){
                this.onclick=function(){
                    window.open("<?=Url::toRoute(['payment/pdf', 'id'=>$model->payment_id])?>");
                };
            });
            
            $(document).on('click', '#change-customer',function(e){
                e.preventDefault();
                $('#customer-modal').modal('show');
            });
            
            $(document).on('click', '#change-customer-confirm', function(e){
                PaymentView.changeCustomer();
            });
        }
        
        this.changeCustomer= function(){
            $.ajax({
                url: "<?= Url::to(['/checkout/payment/change-customer'])?>",
                data: {payment_id: "<?=$model->payment_id?>", customer_id: $('#payment-customer_id').val()},
                method: 'POST'
            });
        }
        
    };
</script>
<?php $this->registerJs('PaymentView.init()')?>