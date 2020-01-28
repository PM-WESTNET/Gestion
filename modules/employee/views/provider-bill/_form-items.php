
<?php

use app\modules\accounting\models\Account;
use app\modules\config\models\Config;
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
    yii\widgets\Pjax::begin(['id' => 'new_item']);
        $form = ActiveForm::begin([
        'id'=>'item-add-form',
        'action' => ['add-item', 'id' => $model->employee_bill_id],
        'options' => ['data-pjax' => true ]]);
?>

<input type="hidden" name="EmployeeBillItem[employee_bill_id]" value="<?=$model->employee_bill_id?>"/>

<div class="row">

    <div class="col-sm-9 col-md-4">
        <?= $form->field($item, 'description')->textInput()  ?>
    </div>
    <div class="col-sm-9 col-md-4">
        <?= $form->field($item, 'account_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(Config::getValue('parent_outflow_account')), 'account_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>
    <div class="col-sm-9 col-md-2">
        <?= $form->field($item, 'amount')->textInput()  ?>
    </div>
    <div class="col-sm-9 col-md-2">
        <label style="display: block">&nbsp;</label>
            <div class="btn btn-primary" id="item-add">
            <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Add') ?>
        </div>
    </div>
</div>
<?php
    ActiveForm::end();
    yii\widgets\Pjax::end()
?>
