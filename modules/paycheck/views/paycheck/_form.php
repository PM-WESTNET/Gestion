<?php

use app\modules\accounting\models\MoneyBox;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\config\models\Config;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Paycheck */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="paycheck-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if ($from_thrid_party) {
        echo Html::hiddenInput('Paycheck[is_own]', '0');
    } else {
        if ($for_payment) {
            echo $form->field($model, 'is_own')->checkbox();
        } else {
            echo Html::hiddenInput('Paycheck[is_own]', '0');
        }
    }
    ?>

    <?= $form->field($model, 'to_order')->checkbox() ?>

    <?= $form->field($model, 'crossed')->checkbox() ?>

    <?php
    echo $this->render('@app/modules/accounting/views/money-box-account/_selector', [
        'model' => $model,
        'form' => $form,
        'style' => 'vertical',
        'money_box_id_name' => 'money_box_id',
        'money_box_id_access' => 'money_box_id',
        'id' => 'bank-account-selector',
        'dropDownSuffix' => '_bank',
        'moneyBoxType' => Config::getValue('money_box_bank'),
        'from_thrid_party' => $from_thrid_party
    ]);
    ?>

    <?php
    if ($model->checkbook !== null) {
        $data = [$model->checkbook_id => $model->checkbook->last_used];
    } else {
        $data = [];
    }
    ?>

    <div id="div-checkbook">
        <?php
        if ($from_thrid_party != 1 && $from_thrid_party != true) {
    echo $form->field($model, 'checkbook_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'checkbook_id'],
        'data' => $data,
        'pluginOptions' => [
            'depends' => ['money_box_account_id_bank'],
            'initDepends' => 'money_box_account_id_bank',
            'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass' => Yii::t('paycheck', 'Money Box Account')]),
            'url' => Url::to(['/paycheck/paycheck/checkbooks']),
            'initialize' => $model->isNewRecord ? false : true,
        ]
    ]);
        }
        ?>
</div>

    <?= $form->field($model, 'document_number')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'business_name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR', 'dateFormat' => 'dd-MM-yyyy', 'options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'due_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR', 'dateFormat' => 'dd-MM-yyyy', 'options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>




<script>
    var PaycheckForm = new function () {
        this.init = function () {
            $(document).on("change", "#paycheck-is_own", function () {
                PaycheckForm.owned($(this).is(":checked"));
            });

            $(document).on("change", "#checkbook_id", function () {
                PaycheckForm.findNextPaycheck();
            });

            $(document).on("change", "#paycheck-date", function () {
                PaycheckForm.updateDueDate();
            });

            PaycheckForm.owned($("#paycheck-is_own").is(":checked"));
            
            
            };

        this.owned = function (is_own) {
            if (!is_own) {
                $("#money_box_account_id").val("");
                $(".field-paycheck-money_box_account_id").hide(400);
                $(".field-paycheck-checkbook_id").hide(400);
                $(".field-paycheck-document_number").show(400);
                $(".field-paycheck-business_name").show(400);
            } else {
                $("#money_box_id").trigger("change");
                $("#money_box_account_id").val("");
                $(".field-paycheck-money_box_account_id").show(400);
                $(".field-paycheck-checkbook_id").show(400);
                $(".field-paycheck-document_number").hide(400);
                $(".field-paycheck-business_name").hide(400);
            }
        };

        this.updateDueDate = function () {
            var date = $("#paycheck-date").datepicker("getDate");
            date.setDate(date.getDate() + 30);
            $("#paycheck-due_date").datepicker("setDate", date);
        };
        this.findNextPaycheck = function () {
            var data = new Object;
            data.checkbook_id = $('#checkbook_id').val();
            $.ajax({
                url: '<?= \yii\helpers\Url::toRoute(['/paycheck/checkbook/get-last-number-used']) ?>',
                data: data,
                dataType: 'json',
                type: 'get'
            }).done(function(json){
                console.log(json);
                $("#paycheck-number").val(json + 1);
            });
        };
    };
</script>
<?php $this->registerJs("PaycheckForm.init();"); ?>