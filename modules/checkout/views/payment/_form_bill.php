<?php

use app\modules\accounting\models\MoneyBox;
use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;use yii\grid\GridView;use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;use yii\widgets\ActiveForm;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">
    <?php $form = ActiveForm::begin([
        'id' => 'payment-form',
        'action' => ['pay-bill', 'bill'=> $bill->bill_id, 'payment'=> $model->payment_id]
    ]); ?>
    <?= Html::hiddenInput('Payment[status]', $model->status, ['id'=>'payment_status']) ?>
    <?= Html::hiddenInput('Payment[payment_id]', $model->payment_id, ['id'=>'payment_id']) ?>
    <?php ActiveForm::end(); ?>

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
                  
       
    </div>
    
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <?=$model->customer->name . " - " . $bill->billType->name .  " - " .$bill->number?>
                </strong>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4 text-center">
                        <strong><?= Yii::t('app', 'Date'); ?></strong>
                        <br/>
                        <?= $model->date ?>
                    </div>
                    <div class="col-sm-4 text-center">
                        <strong><?= Yii::t('app', 'Amount to Pay'); ?></strong>
                        <br/>
                        <?= Yii::$app->formatter->asCurrency($model->amount) ?>
                    </div>
                    <div class="col-sm-4 text-center">
                        <strong><?= Yii::t('app', 'Concept'); ?></strong>
                        <br/>
                        <?= $model->concept ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $bill->getBillDetails(),
                'pagination' => false
            ]);
            echo \yii\bootstrap\Collapse::widget([
                'items' => [
                    [
                        'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Bill'),
                        'content' => $this->render('@app/modules/sale/views/bill/_view',[
                            'model'=>$bill,
                            'detailsProvider'=>$dataProvider,
                            'url_open'=> Url::toRoute(['/checkout/payment/open-bill', 'bill' => $bill->bill_id, 'payment'=>$model->payment_id]) ]),
                        'encode' => false,
                    ],
                ]
            ]);

        ?>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'Detail') ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-8">
                        <?php echo $this->render('_payment_items', ['model'=>$model, 'item'=>new \app\modules\checkout\models\PaymentItem()]);?>
                    </div>

                    <div class="col-sm-4">
                        <div class="row">
                            <?php
                            // Listado de detalles
                            \yii\widgets\Pjax::begin(['id'=>'w_items']);

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
                                    'amount:currency',
                                    [
                                        'class' => 'app\components\grid\ActionColumn',
                                        'template'=>'{delete}',
                                        'buttons'=>[
                                            'delete'=>function ($url, $model, $key) {
                                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', "#",
                                                    [
                                                        'data-url' => yii\helpers\Url::toRoute(['payment/delete-item', 'payment_item_id'=>$key]),
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
                            <div class="col-sm-9 col-md-6 text-center">
                                <label><?=Yii::t("app", "Balance")?></label>
                                <div><label><?=Yii::$app->formatter->asCurrency($model->amount - $model->calculateTotalItems())?></label></div>
                            </div>

                            <div class="col-sm-9 col-md-6 text-center">
                                <label><?=Yii::t("app", "Total of Payment")?></label>
                                <div><label><?=Yii::$app->formatter->asCurrency($model->calculateTotalItems())?></label></div>
                            </div>
                            <?php \yii\widgets\Pjax::end(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <p id="paymentButtons" class="text-right">
            <a id="cancelPayment" class="btn btn-danger" style="display:<?=(($model->status == 'draft' )? 'relative' : 'none' ) ?>">
                <span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app','Cancel'); ?>
            </a>
            <a id="savePayment" class="btn btn-success" style="display:<?=(($model->calculateTotalItems() == $model->amount && round($model->amount) != 0 )? '' : 'none' ) ?>">
                <?= Yii::t('app','Save'); ?>
            </a>
        </p>

    

</div>
<script>
    var Payment = new function(){
        this.init = function(){
            $(document).off("click", "#cancelPayment")
                .on("click", "#cancelPayment", function(){
                Payment.cancel();
            });
            $(document).off("click", "#closePayment")
                       .on("click", "#closePayment", function(){
                Payment.close();
            });
            $(document).off("click", "#savePayment")
                .on("click", "#savePayment", function(){
                Payment.save();
            });

            $(document).off('click', '#item-add')
                .on('click', '#item-add', function(){
                Payment.addItem();
            });
            $(document).off('click', '.payment-item-delete')
                .on('click', '.payment-item-delete', function(){
                Payment.removeItem(this);
            });

        };

        this.save = function() {
            $("#payment-form").submit();
        }

        this.cancel = function() {
            $("#payment-form").attr('action', '<?=Url::to(['/checkout/payment/cancel-payment', 'bill' => $bill->bill_id, 'payment'=>$model->payment_id])?>');
            $("#payment-form").submit();
        }

        this.addItem = function() {
            var $form = $("#payment-item");
            var data = $form.serialize();

            $.ajax({
                url: $form.attr('action'),
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){
                if(json.status=='success'){
                    Payment.update();
                }else{
                    //Importante:
                    //https://github.com/yiisoft/yii2/issues/5991 #7260
                    //TODO: actualizar cdo este disponible
                    for(error in json.errors){
                        $('.field-paymentitem-'+error).addClass('has-error');
                        $('.field-paymentitem-'+error+' .help-block').text(json.errors[error]);
                    }
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
                        Payment.update();
                    }else{
                    }
                });
            }
        }

        this.update = function() {
            $.ajax({
                url: '<?=Url::to(['/checkout/payment/pay-bill', 'bill'=>$bill->bill_id, 'payment'=>$model->payment_id])?>',
                dataType: 'html',
                type: 'post'
            }).done(function(html){
                $('#new_item').replaceWith(
                    $(html).find('#new_item')
                );
                $('#w_items').replaceWith(
                    $(html).find('#w_items')
                );
                $('#paymentButtons').replaceWith(
                    $(html).find('#paymentButtons')
                );

                $("#payment_method_id").trigger("change");
            });
        }
    }
</script>
<?php $this->registerJs('Payment.init();'); ?>