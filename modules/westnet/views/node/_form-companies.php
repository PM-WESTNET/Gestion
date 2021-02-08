
<?php

use app\modules\accounting\models\Account;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\accounting\models\OperationType;
use app\modules\partner\models\Partner;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\modules\westnet\models\Node */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id'=>'companies-add-form',
    'action' => ['add-companies', 'id' => $model->node_id],
    'enableClientValidation' => false,
    'options' => ['data-pjax' => true, 'onsubmit'=> 'return false' ]]);
?>
<input type="hidden" name="NodeHasCompanies[node_has_companies_id]" id="node_has_companies-node_has_companies_id"
       value="<?php echo $item->node_has_companies_id?>">
<input type="hidden" name="NodeHasCompanies[node_id]" id="node_has_companies-node_id" value="<?php echo $item->node_id?>">
<div class="row">
    <div class="col-sm-8 col-md-8">
        <?php echo \app\components\companies\CompanySelector::widget([
            'attribute'=>'company_id',
            'model' => $item,
            'conditions' => 'parent_id is null and status = \'enabled\'',
            'label' => Yii::t('westnet', 'Companies')
        ]);?>
    </div>
    <div class="col-sm-4 col-md-4">
        <label style="display: block">&nbsp;</label>
        <a class="btn btn-<?=($item->isNewRecord  ? 'success' : 'primary' ) ?>" id="companies-add">
            <span id="companies-add-span" class="glyphicon glyphicon-<?=($item->isNewRecord  ? 'plus' : 'pencil' ) ?>"><?= Yii::t('app', ($item->isNewRecord  ? 'Add' : 'Update' )) ?></span>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-sm-4 col-md-4">
        <?php echo \app\components\companies\CompanySelector::widget([
            'attribute'=>'first_company_id',
            'model' => $item,
        ]);?>
    </div>
    <div class="col-sm-4 col-md-4">
        <?php echo \app\components\companies\CompanySelector::widget([
            'attribute'=>'second_company_id',
            'model' => $item,
        ]);?>
    </div>
    <div class="col-sm-4 col-md-4">
        <?php echo \app\components\companies\CompanySelector::widget([
            'attribute'=>'third_company_id',
            'model' => $item,
        ]);?>
    </div>
</div>
<?php
    ActiveForm::end();
?>
