    <?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\FundingPlan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="funding-plan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'product_id' ) ?>

    <?= $form->field($model, 'qty_payments')->textInput() ?>

    <?= $form->field($model, 'amount_payment')->textInput() ?>

    <div class="form-group">
        <label class="control-label"><?php echo Yii::t('app', 'Taxes per fee') ?></label>
        <label class="form-control" id="final-taxes"><?php echo Yii::$app->formatter->asCurrency($model->getFinalTaxesAmount()) ?></label>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo Yii::t('app', 'Fee amount with Taxes') ?></label>
        <label class="form-control" id="final-amount"><?php echo Yii::$app->formatter->asCurrency($model->getFinalAmount()) ?></label>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo Yii::t('app', 'Total amount funded') ?></label>
        <label class="form-control" id="final-total-amount"><?php echo Yii::$app->formatter->asCurrency($model->getFinalTotalAmount()) ?></label>
    </div>

    <?= $form->field($model, 'status')->dropDownList(['enabled'=>Yii::t('app','Enabled'),'disabled'=>Yii::t('app','Disabled')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
    var FundingPlanForm = new function() {
        this.init = function() {
            $(document).off("blur", "#fundingplan-amount_payment,#fundingplan-qty_payments")
                        .on("blur", "#fundingplan-amount_payment,#fundingplan-qty_payments", function(){
                FundingPlanForm.actualizarMontos();
            });

        }

        this.actualizarMontos = function(){
            if($('#fundingplan-amount_payment').val() != 0 && $('#fundingplan-qty_payments').val() != 0) {
                $.ajax({
                    url: '<?php echo Url::toRoute(['funding-plan/totals', 'id'=>$model->product_id]) ?>',
                    method: 'post',
                    data: $('#w0').serializeArray(),
                    success: function(data) {
                        $('#final-total-amount').html(data.finalTotalAmount);
                        $('#final-amount').html(data.finalAmount);
                        $('#final-taxes').html(data.finalTaxes);
                    }
                });
            }
        }
    }
</script>
<?php $this->registerJs('FundingPlanForm.init();'); ?>