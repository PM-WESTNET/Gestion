<?php

use app\modules\provider\models\Provider;
use app\modules\provider\models\ProviderBillHasTaxRate;
use app\modules\provider\models\ProviderBillItem;
use app\modules\sale\models\BillType;
use app\modules\sale\models\TaxRate;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\ProviderBill */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provider-bill-form">

    <?php $form = ActiveForm::begin(['id'=>'provider-bill-form', 'action' => [ ( $model->isNewRecord ? 'create' : 'update' ), 'id'=>$model->provider_bill_id ,'provider' => $model->provider_id, 'from'=>$from] ]); ?>

    <?= Html::hiddenInput('ProviderBill[status]', $model->status, ['id'=>'bill_status']) ?>
    <?= Html::hiddenInput('pay_after_save', 'false', ['id'=>'pay_after_save']) ?>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?php
        echo $this->render('@app/modules/partner/views/partner-distribution-model/_selector', ['model' => $model, 'form'=>$form]);
    ?>

    <div class="form-group">
    <?php if (!$model->provider) { ?>
        <div class="input-group" style="z-index:0;">
            <?= $form->field($model, 'provider_id')->widget(Select2::className(),[
                'data' => yii\helpers\ArrayHelper::map(Provider::find()->all(), 'provider_id', 'name' ),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);
            ?>
        </div>
    <?php } ?>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'date')->widget(yii\jui\DatePicker::className(),[
            'language' => Yii::$app->language,
            'dateFormat' => 'dd-MM-yyyy',
            'options'=>[
                'class'=>'form-control filter dates',
                'placeholder'=>Yii::t('app','Date'),
                'autocomplete' => "off"
            ]
        ]);
        ?>
        <div class="help-block"></div>
    </div>
    <?= $form->field($model, 'bill_type_id')->dropDownList( ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name'), ['id'=>'bill-type'] ) ?>

    <div class="row inline">
        <div class="col-sm-6">
            <?= $form->field($model, 'number1')->textInput(['maxlength' => 4, 'id' => 'number1']) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'number2')->textInput(['maxlength' => 8, 'id' => 'number2']) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

    <?php ActiveForm::end(); ?>

    <?php if ($dataProvider!==null) { ?>
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
            <h3 class="panel-title"><?= Yii::t('app', 'Items') ?></h3>
        </div>
        <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
            <?php
                // Formulario para los Items
                echo $this->render('_form-items', ['model'=>$model,'dataProvider'=>$itemsDataProvider, 'item'=>new ProviderBillItem()]);

                // Listado de Items
                \yii\widgets\Pjax::begin(['id'=>'items']);
                echo GridView::widget([
                    'id'=>'grid',
                    'dataProvider' => $itemsDataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'description',
                        [
                            'label' => Yii::t("accounting", "Account"),
                            'value' => function($model){
                                return ($model->account ? $model->account->name : '' );
                            }
                        ],
                        'amount:currency',
                        [
                            'class' => 'app\components\grid\ActionColumn',
                            'template'=>'{delete}',
                            'buttons'=>[
                                'delete'=>function ($url, $model, $key) {
                                    return '<a class="removeItem btn btn-danger" href="#" data-url="'.Url::toRoute(['provider-bill/delete-item', 'provider_bill_id'=>$model->provider_bill_id, 'provider_bill_item_id'=>$model->provider_bill_item_id]).
                                    '" title="'.Yii::t('app','Delete').'" data-confirm-text="'.Yii::t('yii','Are you sure you want to delete this item?').'" ><span class="glyphicon glyphicon-trash"></span></a>';
                                }
                            ]
                        ],
                    ],
                    'options'=>[
                        'style'=>'margin-top:10px;'
                    ]
                ]);
            ?>
            <input type="hidden" id="total_items" value="<?=$model->calculateItems()?>"/>
            <div class="row">
                <div class="col-sm-9 col-md-3">
                    <label><?=Yii::t("app", "Total of Items")?></label>
                </div>
                <div class="col-sm-9 col-md-3">
                    <label><?= Yii::$app->formatter->asCurrency($model->calculateItems())?></label>
                </div>
            </div>
            <?php \yii\widgets\Pjax::end();?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"  data-toggle="collapse" data-target="#panel-body-taxes" aria-expanded="true" aria-controls="panel-body-taxes">
            <h3 class="panel-title" ><?= Yii::t('app', 'Taxes') ?></h3>
        </div>
        <div class="panel-body collapse in" id="panel-body-taxes" aria-expanded="true">
            <?php
            // Formulario para los impuestos
            echo $this->render('_form-taxes', ['model'=>$model,'dataProvider'=>$dataProvider, 'pbt'=>new ProviderBillHasTaxRate()]);

            // Listado de impuestos
            \yii\widgets\Pjax::begin(['id'=>'taxes']);
            echo GridView::widget([
                'id'=>'grid',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t("app", "Tax"),
                        'value' => function($model){
                            return $model->taxRate->tax->name . " " . $model->taxRate->name;
                        }
                    ],
                    'amount:currency',
                    [
                        'class' => 'app\components\grid\ActionColumn',
                        'template'=>'{delete}',
                        'buttons'=>[
                            'delete'=>function ($url, $model, $key) {
                                return '<a class="removeTax btn btn-danger" href="#" data-url="'.Url::toRoute(['provider-bill/delete-tax', 'provider_bill_id'=>$model->provider_bill_id, 'tax_rate_id'=>$model->tax_rate_id]).
                                '" title="'.Yii::t('app','Delete').'" data-confirm-text="'.Yii::t('yii','Are you sure you want to delete this item?').'"><span class="glyphicon glyphicon-trash"></span></a>';
                            }
                        ]
                    ],
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
            <div class="row">
                <div class="col-sm-9 col-md-3">
                    <label><?=Yii::t("app", "Total of Taxes")?></label>
                </div>
                <div class="col-sm-9 col-md-3">
                    <label><?= Yii::$app->formatter->asCurrency($model->calculateTaxes())?></label>
                    <input type="hidden" id="total_taxes" value="<?= $model->calculateTaxes() + $model->calculateTaxesWithZeroPercentage() ?>"/>
                </div>
            </div>
            <?php \yii\widgets\Pjax::end();?>
        </div>
    </div>
    <?php    } ?>

    <?php if (!$model->isNewRecord) { ?>
    <?php \yii\widgets\Pjax::begin(['id'=>'totals']); ?>
    <div class="row">
        <div class="col-sm-9 col-md-3">
            <label><?=Yii::t("app", "Total of Invoice")?></label>
        </div>
        <div class="col-sm-9 col-md-2">
            <label><?= Yii::$app->formatter->asCurrency($model->calculateTotal())?></label>
        </div>
    </div>
    <?php \yii\widgets\Pjax::end();?>
    <?php } ?>

    <div class="col-sm-12 col-xs-12 no-padding row">
        <?php if ($model->isNewRecord) { ?>
            <a id="saveBill" onclick="ProviderBill.save();" class="btn <?=($model->isNewRecord ? 'btn btn-success' : 'btn btn-success')?>">
                <!-- <span class='glyphicon glyphicon-plus'></span>              -->
                <?= Yii::t('app','Add Detail'); ?>
            </a>
        <?php } ?>

        <?php if (!$model->isNewRecord ) { ?>
        <a id="closeBill" onclick="ProviderBill.closeAndPay();" class="btn btn-success" style="<?=($model->calculateTotal() == ( $model->calculateItems() + $model->calculateTaxes() ) &&
            $model->total == ( $model->calculateItems() + $model->calculateTaxes() ) &&
            $model->calculateTotal() > 0 ? '' : 'none' ) ?>">
                <!-- <span class='glyphicon glyphicon-remove'></span>  -->
                <?=Yii::t('app','Pay'); ?>
            </a>
            <a id="closeBill" onclick="ProviderBill.close();" class="btn btn-info" style="<?=($model->calculateTotal() == ( $model->calculateItems() + $model->calculateTaxes() ) &&
            $model->total == ( $model->calculateItems() + $model->calculateTaxes() ) &&
            $model->calculateTotal() > 0 ? '' : 'none' ) ?>">
                <!-- <span class='glyphicon glyphicon-remove'></span>  -->
                <?=Yii::t('accounting','To Current Account'); ?>
            </a>
        <?php } ?>
    </div>
</div>

<script>
    var ProviderBill = new function() {
        this.init = function () {

            $('#number1').inputmask('9999', { numericInput: true, placeholder: "0" });
            $('#number2').inputmask('99999999', { numericInput: true, placeholder: "0" });

            $('#number1').keyup(function(evt) {
                var key = evt.keyCode;
                if(evt.keyCode == 109){
                    document.getElementById("number2").focus();
                }
            });

            // Eventos de impuestos
            $(document).on("click","#tax-add", function(){
                ProviderBill.addTax();
            });
            $("#new_tax").on("pjax:end", function() {
                $.pjax.reload({container:"#taxes", async:false});
                $.pjax.reload({container:"#totals", async:false});
            });

            $(document).on("click",".removeTax", function(event){
                ProviderBill.removeTax(this, event);
            });

            $(document).on("blur","#providerbill-net", function(event){
                ProviderBill.calculateTotals();
            });

            // Eventos de Items
            $(document).on("click","#item-add", function(){
                ProviderBill.addItem();
            });

            $(document).on("click",".removeItem", function(event){
                ProviderBill.removeItem(this, event);
            });
            $("#new_item").on("pjax:end", function() {
                $.pjax.reload({container:"#items", async:false});
                $.pjax.reload({container:"#totals", async:false});
            });
            $(document).on('pjax:complete', function() {
                ProviderBill.calculateTotals();
            });
            ProviderBill.calculateTotals();

        }

        this.save = function() {
            $("#provider-bill-form").submit();
        }

        this.calculateTotals = function() {
            // var totalBill  = new Number($('#providerbill-net').val());
            var totalItems = new Number($('#total_items').val());
            var totalTaxes = new Number($('#total_taxes').val());
            var total = (isNaN( parseFloat(totalItems+totalTaxes)) ? 0 : parseFloat(totalItems+totalTaxes) );

            $("#divTotal").html( '$' + total.toFixed(2));
            $("#divTotalItems").html( '$' + totalItems.toFixed(2));

            $("#divTotalItems").removeClass('alert-danger');
            return total;
        }

        this.close = function() {
            if (ProviderBill.calculateTotals()) {
                if (confirm('<?=Yii::t('app', 'Are you sure you want to close the bill?') . "\\n" . Yii::t('app', 'We will apply the accounting movements.')?>')) {
                    $("#bill_status").val("closed");
                    $("#provider-bill-form").submit();
                }
            } else {
                alert('<?=Yii::t('app', 'The total must be greater than 0.')?>');
            }
        }

        this.closeAndPay = function() {
            if (ProviderBill.calculateTotals()) {
                if (confirm('<?=Yii::t('app', 'Are you sure you want to close the bill?') . "\\n" . Yii::t('app', 'We will apply the accounting movements.')?>')) {
                    $("#bill_status").val("closed");
                    $("#pay_after_save").val("true");
                    $("#provider-bill-form").submit();
                }
            } else {
                alert('<?=Yii::t('app', 'The total must be greater than 0.')?>');
            }
        }

        this.addTax = function() {
            $('#tax-add-form').submit();
        }

        this.removeTax = function(elem, event) {
            event.preventDefault();
            var url = $(elem).data('url');
            var confirmText = $(elem).data('confirm-text');
            if (confirm(confirmText)) {
                $.ajax({
                    url: url,
                    type: 'post',
                }).done(function(data) {
                    $.pjax.reload({container:"#taxes", async:false});
                    $.pjax.reload({container:"#totals", async:false});
                    ProviderBill.calculateTotals();
                });
            }
        }

        this.addItem = function() {
            $('#item-add-form').submit();
        }

        this.removeItem = function(elem, event) {
            event.preventDefault();
            var url = $(elem).data('url');
            var confirmText = $(elem).data('confirm-text');
            if (confirm(confirmText)) {
                $.ajax({
                    url: url,
                    type: 'post',
                }).done(function(data) {
                    $.pjax.reload({container:"#items", async:false});
                    $.pjax.reload({container:"#totals", async:false});
                    ProviderBill.calculateTotals();
                });
            }

        }
    }

</script>
<?php  $this->registerJs("ProviderBill.init();"); ?>