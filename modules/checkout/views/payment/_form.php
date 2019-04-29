<?php

use app\modules\accounting\models\MoneyBox;
use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use kartik\widgets\Select2;
use yii\data\ActiveDataProvider;use yii\grid\GridView;use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */
/* @var $form yii\widgets\ActiveForm */

if(!$model->company_id) {
    $model->company_id = ($model->customer ? $model->customer->company_id : null );
}
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'payment-form',
    ]); ?>

    <?= $form->errorSummary($model); ?>

    <?= Html::hiddenInput('Payment[status]', $model->status, ['id'=>'payment_status']) ?>
    <?= Html::hiddenInput('Payment[payment_id]', $model->payment_id, ['id'=>'payment_id']) ?>

    <?= app\components\companies\CompanySelector::widget( ['model' => $model, 'inputOptions' => ['prompt' => Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('app', 'Company')]).'...']]) ?>

    <?php
        echo $this->render('@app/modules/partner/views/partner-distribution-model/_selector', ['model' => $model, 'form'=>$form]);
    ?>

    <?php
        if (!$model->customer) {

            echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id']);

        } else { ?>
        <?= Html::hiddenInput('Payment[customer_id]', $model->customer_id, ['id'=>'customer_id']) ?>
    <?php }  ?>


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
            ]
        ]);
        ?>
    </div>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'concept')->textInput(['maxlength' => 255]) ?>

    <?php ActiveForm::end(); ?>

    <?php
    if(!$model->isNewRecord) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Detail') ?></h3>
        </div>
        <div class="panel-body">

        <?php echo $this->render('_payment_items', ['model'=>$model, 'item'=>new \app\modules\checkout\models\PaymentItem()]);

        // Listado de detalles
        \yii\widgets\Pjax::begin(['id'=>'w_items']);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getPaymentItems(),
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
            </div>
        </div>
        </div>

        <div class="row text-center" id="totals">
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Total of Payment")?></label>
                <div><label><?=Yii::$app->formatter->asCurrency($model->amount)?></label></div>
            </div>
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Total of Detail")?></label>
                <div><label><?=Yii::$app->formatter->asCurrency($model->calculateTotalItems());?></label></div>
            </div>
            <div class="col-sm-9 col-md-3">
                <label><?=Yii::t("app", "Balance")?></label>
                <div><label><?=Yii::$app->formatter->asCurrency($model->amount - $model->calculateTotalItems())?></label></div>
            </div>
        </div>

        <div class="row" id="message">
            <?php
            if ($model->calculateTotalItems() != $model->amount && round($model->amount) != 0 ) {
                ?>
                <div class="col-sm-12 alert alert-danger">
                    <?php echo Yii::t('app', 'The balance must be equal to 0.') ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
    <?php } ?>
    <div class="form-group" id="saveButtons">
        <a id="closePayment" class="btn btn-success" style="display:<?=($model->canClose() ? '' : 'none' ) ?>">
            <span class="glyphicon glyphicon-ok"></span>
            <?= Yii::t('app','Close Payment'); ?>
        </a>
        <a id="savePayment" class="btn btn-default"><?= Yii::t('app',($model->isNewRecord ? 'Next' : 'Save draft')); ?></a>
    </div>

</div>


<script>
    var Payment = new function(){
        this.init = function(){
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

        this.close = function() {
            if(confirm('<?= Yii::t('app', 'Are you sure you want to close the Payment?') ?>')) {
                var url = '<?=Url::to(['/checkout/payment/close', 'payment_id'=>$model->payment_id])?>';
                $("#payment-form").attr("action", url )
                $("#payment-form").submit();
            }
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
                url: '<?=Url::to(['/checkout/payment/create', 'customer'=>$model->customer_id, 'payment'=>$model->payment_id])?>',
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
                $('#message').replaceWith(
                    $(html).find('#message')
                );
                $("#payment_method_id").trigger("change");
                if (jQuery('#money_box_account_id_bank').data('depdrop')) { jQuery('#money_box_account_id_bank').depdrop('destroy'); }
                jQuery('#money_box_account_id_bank').depdrop(eval(jQuery('#money_box_account_id_bank').attr('data-krajee-depdrop')));
                if (jQuery('#money_box_account_id_small').data('depdrop')) { jQuery('#money_box_account_id_small').depdrop('destroy'); }
                jQuery('#money_box_account_id_small').depdrop(eval(jQuery('#money_box_account_id_small').attr('data-krajee-depdrop')));
            });

        }
    }
</script>
<?php $this->registerJs('Payment.init();'); ?>
