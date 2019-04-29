<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */
/* @var $form yii\widgets\ActiveForm */
$total = abs($payment->accountTotal());
?>

<div class="payment-form">
    <div class="row">
        <div class="col-sm-12">
            <!-- Inicio Seleccion de datos para facturacion -->
            <div class="panel panel-default">
                <div class="panel-body collapse in" id="panel-body-payment-plan" aria-expanded="true">

                    <?php $form = ActiveForm::begin([
                        'action' => ['create', 'customer_id' => $customer->customer_id],
                        'id' => 'payment-plan-form',
                    ]); ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="hidden" name="PaymentPlan[original_amount]" id="original_amount" value="<?php echo $total?>"/>
                                <label for="original_amount" class="control-label"><?php echo Yii::t('app', 'Total Debt Amount') ?></label>
                                <div class="form-control">
                                    <?php echo  Yii::$app->formatter->asCurrency( abs($payment->accountTotal()) ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'payment_plan_amount')->textInput() ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                                <?php echo $form->field($model, 'from_date')->widget(DatePicker::classname(), [
                                    'type' => 1,
                                    'language' => Yii::$app->language,
                                    'model' => $model,
                                    'attribute' => 'from_date',
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
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?php echo $form
                                    ->field($model, 'apply')
                                    ->dropDownList( ['-1'=>Yii::t("app", "Discount"), '1'=>Yii::t("app", "Surcharge")], ['prompt' => Yii::t('app', 'Select')] );
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'value_applied')->textInput() ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?php
                            $fees =[];
                            for($i=1;$i<=12;$i++){
                                $fees[$i] = Yii::t("app", "{number} Fee/s", ['number'=>$i]);
                            }

                            echo $form
                                ->field($model, 'fee')
                                ->dropDownList( $fees  );
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <br>
                            <?= $form->field($model, 'create_bill')->checkbox(['label' => Yii::t('app','Create a Credit Note')])?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_id" class="control-label"><?php echo Yii::t('app', 'Fee amount') ?></label>
                                <div class="form-control" id="fee-amount">
                                    <?php echo  Yii::$app->formatter->asCurrency( abs($payment->accountTotal()) ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_id" class="control-label"><?php echo Yii::t('app', 'Total to Pay') ?></label>
                                <div class="form-control" id="total">
                                    <?php echo  Yii::$app->formatter->asCurrency( abs($payment->accountTotal()) ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="saveButtons">
                        <a id="btnSave" class="btn btn btn-success"><?= Yii::t('app', 'Generate'); ?></a>

                        <?php
                            echo Html::a("<span class='glyphicon btn-danger'></span> " .Yii::t('app', 'Cancel'), ['/checkout/payment/current-account','customer'=>$customer->customer_id], [
                                'class' => 'btn btn-warning',
                            ]);
                        ?>
                    </div>
                    <input type="hidden" name="PaymentPlan[final_amount]" id="final_amount" value="<?php echo $total?>"/>
                    <?php ActiveForm::end(); ?>
                </div>
            </div> <!-- Fin Seleccion de plan de pago -->

        </div>
    </div>
</div>
<script>
    var PaymentPlan = new function(){
        this.init = function(){
            $(document).off('change', '#paymentplan-apply,#paymentplan-value_applied,#paymentplan-fee,#paymentplan-payment_plan_amount')
                .on('change', '#paymentplan-apply,#paymentplan-value_applied,#paymentplan-fee,#paymentplan-payment_plan_amount', function(){
                PaymentPlan.calcularTotal();
            });

            $(document).off('click', '#btnSave')
                .on('click', '#btnSave', function(){
                PaymentPlan.save();
            });

        };

        this.calcularTotal = function () {
            var total_inicial = new Number($('#paymentplan-payment_plan_amount').val());
            var fee = $('#paymentplan-fee').val();
            var value = new Number($('#paymentplan-value_applied').val());
            if(value==0) {
                value
            }
            var apply = new Number($('#paymentplan-apply').val());

            if( !isNaN(new Number(value)) ) {
                total = new Number( Math.abs(total_inicial * ( value==0 ? 1 : ((apply)+( value/100 ))  )) );
                $('#fee-amount').html("$ " + new Number(total / fee ).toFixed(2));
                $('#total').html("$ " + total.toFixed(2));
                $('#final_amount').val(total.toFixed(2));
            }
        }

        this.save = function(){
            if(confirm('<?php echo Yii::t('app', 'You are sure to create the payment plan ?') ?>')){
                $("#payment-plan-form").submit();
            }
        }

    }
</script>
<?php $this->registerJs('PaymentPlan.init();'); ?>