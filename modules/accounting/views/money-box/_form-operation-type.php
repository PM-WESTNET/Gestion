
<?php

use app\modules\accounting\models\Account;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\accounting\models\OperationType;
use kartik\widgets\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'id'=>'operation-type-add-form',
    'action' => ['add-operation-type', 'id' => $model->money_box_id],
    'enableClientValidation' => false,
    'options' => ['data-pjax' => true, 'onsubmit'=> 'return false' ]]);
?>
<input type="hidden" name="MoneyBoxHasOperationType[money_box_has_operation_type_id]" id="moneyboxhasoperationtype-money_box_has_operation_type_id"
       value="<?php echo $item->money_box_has_operation_type_id?>">
<input type="hidden" name="MoneyBoxHasOperationType[money_box_id]" id="moneyboxhasoperationtype-money_box_id"
       value="<?php echo $model->money_box_id?>">
<div class="row">
    <div class="col-sm-4 col-md-4">
        <?= $form->field($item, 'money_box_account_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(MoneyBoxAccount::find()->where(['money_box_id'=>$model->money_box_id])->all(), 'money_box_account_id', 'number' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>
    <div class="col-md-8">
        <label style="display: block">&nbsp;</label>
        <div class="form-group">
            <span style="color:blue; font-size:14px"> * Ingrese la Cuenta Monetaria solo cuando el Tipo de Movimiento pueda afectar a distintas cuentas contables.</span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3 col-md-3">
        <?= $form->field($item, 'operation_type_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(OperationType::find()->all(), 'operation_type_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>
    <div class="col-sm-3 col-md-3">
        <div class="form-group">
            <?= $form->field($item, 'account_id')->widget(Select2::className(),[
                'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="col-sm-3 col-md-3">
        <?php echo $form->field($item, 'code')->textInput()?>
    </div>

    <div class="col-sm-2 col-md-2">
        <label style="display: block">&nbsp;</label>
        <a class="btn btn-<?=($item->isNewRecord  ? 'success' : 'primary' ) ?>" id="operation-type-add">
            <span id="operation-type-add-span" class="glyphicon glyphicon-<?=($item->isNewRecord  ? 'plus' : 'pencil' ) ?>"></span><?= Yii::t('app', ($item->isNewRecord  ? 'Add' : 'Update' )) ?>
        </a>
    </div>

</div>
<?php
    ActiveForm::end();
?>
