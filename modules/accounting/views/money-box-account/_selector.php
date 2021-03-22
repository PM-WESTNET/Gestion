<?php
use app\modules\accounting\models\MoneyBox;
use app\modules\config\models\Config;
use kartik\widgets\DepDrop;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$style = (isset($style) ? $style : 'vertical') ;
$from_thrid_party = (isset($from_thrid_party) ? $from_thrid_party : false) ;

if (empty($id)){
    $id = "bank-account-selector";
}
if (empty($moneyBoxType)){
    $moneyBoxType = Config::getValue('money_box_bank');
}
if (empty($dropDownSuffix)){
    $dropDownSuffix = rand(111, 999);
}

?>
<div id="<?= $id ?>" style="<?=((!isset($showSelector)||$showSelector)? "":"display: none")?>">
    <?php if ($style=='horizontal') { ?>
        <div class="col-sm-9 col-md-6">
    <?php } ?>
            <?php
                echo $form->field($model,  'money_box_id' )
                    ->dropDownList(ArrayHelper::map(MoneyBox::findByMoneyBoxType($moneyBoxType)->all(),'money_box_id', 'name'), [
                        'separator'=>'<br/>',
                        'prompt'=> Yii::t('app', 'Select {modelClass}', ['modelClass'=>Yii::t('accounting','Money Box')]),
                        'id' => 'money_box_id' . $dropDownSuffix,
                    ])->label(Yii::t('paycheck', 'Money Box'));
            ?>
    <?php if ($style=='horizontal') { ?>
        </div>
    <?php } ?>


    <?php if ($style=='horizontal') { ?>
    <div class="col-sm-9 col-md-6" id="div-money-box-account-id">
    <?php } ?>
        <div class="form-group" id="div-money-box-account-id">
            <?php
            if (isset($model->moneyBoxAccount) && $model->moneyBoxAccount!==null) {
                $data = [$model->moneyBoxAccount->money_box_account_id=>$model->moneyBoxAccount->number];
            } else {
                $data = [];
            }
            if (!$from_thrid_party) {
                echo $form->field($model, 'money_box_account_id')->widget(DepDrop::classname(), [
                    'options' => ['id' => 'money_box_account_id' . $dropDownSuffix],
                    'data' => $data,
                    'pluginOptions' => [
                        'depends' => ['money_box_id' . $dropDownSuffix],
                        'initDepends' => 'money_box_id',
                        'placeholder' => Yii::t('app', 'Select {modelClass}', ['modelClass' => Yii::t('paycheck', 'Money Box Account')]),
                        'url' => Url::to(['/accounting/money-box-account/moneyboxaccounts'])
                    ]
                ])->label(Yii::t('accounting', 'Money Box Account'));
            }
            ?>
            <div class="help-block"></div>
        </div>
    <?php if ($style=='horizontal') { ?>
        </div>
    <?php } ?>
</div>