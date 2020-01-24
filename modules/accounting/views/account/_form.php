<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Account */
/* @var $form yii\widgets\ActiveForm */
$this->title = ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')) . ' ' . $model->name;
?>

<div class="account-form col-lg-6">
    <h3><?= Html::encode($this->title) ?></h3>

    <?php $form = ActiveForm::begin(array('options' => array())); ?>

    <div class="form-group">
        <?php echo $form->field($model, 'parent_account_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>

    </div>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?php echo $form->field($model, 'status')->dropDownList([
        Account::ENABLED_STATUS => Yii::t('app', 'Active'),
        Account::DISABLED_STATUS => Yii::t('app', 'Disabled'),
    ]) ?>

    <?= $form->field($model, 'is_usable')->checkbox() ?>

    <div class="form-group">
        <a id="create" onclick="parent.Account.save()" class='<?=($model->isNewRecord ? 'btn btn-success' : 'btn btn-primary')?>'><?= ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')) ?></a>
    </div>

    <?php ActiveForm::end(); ?>

</div>