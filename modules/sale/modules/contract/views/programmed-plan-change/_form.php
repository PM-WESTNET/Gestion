<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\modules\sale\modules\contract\models\Plan;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammedPlanChange */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="programmed-plan-change-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::hiddenInput('contract_id', $contract_id, ['id' => 'pre-selected-contract_id']); ?>


    <div class="row">
        <div class="col-sm-6">
            <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $model, 'attribute' => 'customer_id', 'form' => $form])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'product_id')->widget(DepDrop::class, [
                'options' => ['id' => 'select-product-id'],
                'pluginOptions' => [
                    'depends' => ['programmedplanchange-customer_id'],
                    'placeholder' => Yii::t('app', 'Select ...'),
                    'url' => Url::to(['/sale/contract/plan/get-plans-by-customer'])
                ]
            ])?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'contract_id')->widget(DepDrop::class, [
                'options' => ['id' => 'select-contract-id'],
                'pluginOptions' => [
                    'depends' => ['programmedplanchange-customer_id'],
                    'placeholder' => Yii::t('app', 'Select ...'),
                    'url' => Url::to(['/sale/contract/contract/get-contracts-by-customer']),
                    'params' => ['pre-selected-contract_id']
                ]
            ])?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'date')->widget(DatePicker::class, [
                'pluginOptions' => ['format' => 'dd-mm-yyyy']
            ])?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var PlanChange = new function() {
        var self = this;

        this.init = function(){
            if($('#programmedplanchange-customer_id').val() !== ''){
                $('#programmedplanchange-customer_id').trigger({type: 'select2:select'});
                $('#select-contract-id').val("<?= $contract_id ?>");

                $('#programmedplanchange-customer_id').on('change', function () {
                    console.log('change');
                });



            }


            $(document).off("click", "#plan-show_in_ads").on("click", "#plan-show_in_ads", function(evt){
                self.nameAds($(this).is(':checked'));
            });
        }

        this.nameAds = function(checked) {
            if(checked) {
                $("#ads-name").show();
            } else {
                $("#ads-name").hide();
            }
        }
    }
</script>
<?php $this->registerJs('PlanChange.init()') ?>