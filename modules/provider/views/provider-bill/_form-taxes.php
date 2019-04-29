
<?php

use app\modules\accounting\models\Account;
use app\modules\sale\models\Tax;
use app\modules\sale\models\TaxRate;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */
?>


<?php
    yii\widgets\Pjax::begin(['id' => 'new_tax']);
        $form = ActiveForm::begin([
        'id'=>'tax-add-form',
        'action' => ['add-tax', 'id' => $model->provider_bill_id],
        'options' => ['data-pjax' => true ]]);
?>

<input type="hidden" name="ProviderBillHasTaxRate[provider_bill_id]" value="<?=$model->provider_bill_id?>"/>

<div class="row">
    <div class="col-sm-9 col-md-3">
        <?= $form->field($pbt, 'tax_rate_id')->dropDownList( ArrayHelper::map( Tax::find()->select(["tax_rate.tax_rate_id as tax_id", "CONCAT(tax.name, ' - ', (tax_rate.pct*100), '%') as name"])
            ->leftJoin("tax_rate", "tax.tax_id = tax_rate.tax_id")->all(), "tax_id", "name"), ['prompt' => 'Select'])
        ?>
    </div>
    <div class="col-sm-9 col-md-3">
        <?= $form->field($pbt, 'amount')->textInput()  ?>
    </div>
    <div class="col-sm-9 col-md-2">
        <label style="display: block">&nbsp;</label>
            <div class="btn btn-primary" id="tax-add">
            <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Add') ?>
        </div>
    </div>
</div>
<?php
    ActiveForm::end();
    yii\widgets\Pjax::end()
?>
