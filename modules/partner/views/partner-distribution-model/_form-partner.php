
<?php

use app\modules\accounting\models\Account;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\accounting\models\OperationType;
use app\modules\partner\models\Partner;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id'=>'partner-add-form',
    'action' => ['add-partner', 'id' => $model->partner_distribution_model_id],
    'enableClientValidation' => false,
    'options' => ['data-pjax' => true, 'onsubmit'=> 'return false' ]]);
?>
<input type="hidden" name="PartnerDistributionModelHasPartner[partner_distribution_model_id]" id="partnerhascompany-partner_distribution_model_id"
       value="<?php echo $model->partner_distribution_model_id?>">
<input type="hidden" name="PartnerDistributionModelHasPartner[partner_distribution_model_has_partner_id]" id="partnerhascompany-partner_distribution_model_has_partner_id"
       value="<?php echo $item->partner_distribution_model_has_partner_id?>">
<div class="row">
    <div class="col-sm-6 col-md-6">
        <?= $form->field($item, 'partner_id')->dropDownList(
            yii\helpers\ArrayHelper::map(Partner::find()->all(), 'partner_id', 'name' ),
            ['prompt' => 'Select'] )  ?>

    </div>
    <div class="col-md-4">
        <?= $form->field($item, 'percentage')->textInput() ?>
    </div>

    <div class="col-sm-2 col-md-2">
        <label style="display: block">&nbsp;</label>
        <a class="btn btn-<?=($item->isNewRecord  ? 'success' : 'primary' ) ?>" id="partner-add">
            <span id="partner-add-span" class="glyphicon glyphicon-<?=($item->isNewRecord  ? 'plus' : 'pencil' ) ?>"><?= Yii::t('app', ($item->isNewRecord  ? 'Add' : 'Update' )) ?></span>
        </a>
    </div>

</div>
<?php
    ActiveForm::end();
?>
