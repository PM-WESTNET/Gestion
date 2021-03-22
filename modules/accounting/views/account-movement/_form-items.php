
<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id'=>'item-add-form',
    'action' => ['add-item', 'id' => $model->account_movement_id],
    'enableClientValidation' => false,
    'options' => ['data-pjax' => true, 'onsubmit'=> 'return false' ]]);

?>
<input type="hidden" name="AccountMovementItem[account_movement_item_id]" value="<?=$item->account_movement_item_id?>"/>
<input type="hidden" name="AccountMovementItem[account_movement_id]" value="<?=$model->account_movement_id?>"/>
<div class="col-sm-6 col-md-6">
    <?= $form->field($item, 'account_id')->widget(Select2::className(),[
        'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);
    ?>
</div>
<div class="col-sm-2 col-md-2">
    <?= $form->field($item, 'debit')->textInput()  ?>
</div>
<div class="col-sm-2 col-md-2">
    <?= $form->field($item, 'credit')->textInput()  ?>
</div>
<div class="col-sm-2 col-md-2">
    <label style="display: block">&nbsp;</label>
    <a class="btn btn-success" id="item-add"><span id="item-add-span" class="glyphicon glyphicon-plus"><?= Yii::t('app', 'Add') ?></span></a>
</div>
<?php
    ActiveForm::end();
?>
