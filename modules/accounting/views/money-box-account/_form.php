<?php

use app\modules\accounting\models\Account;
use app\modules\sale\models\Currency;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\accounting\models\MoneyBox;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBoxAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div id="messages">
    
</div>

<div class="money-box-account-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?= $form->field($model, 'money_box_id')->dropdownList(ArrayHelper::map(MoneyBox::find()->all(), 'money_box_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>Yii::t('app','Select an option...')]) ?>

    <?= $form->field($model, 'currency_id')->dropdownList(ArrayHelper::map(Currency::find()->all(), 'currency_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>Yii::t('app','Select an option...')]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'enable')->checkbox() ?>
    
    <?= $form->field($model, 'small_box')->checkbox() ?>
    
    <?= $form->field($model, 'type')->dropDownList(['common' => Yii::t('app', 'Common'), 'daily' => Yii::t('app', 'Daily'), 'small' => Yii::t('app', 'Small')], ['prompt'=> Yii::t('app','Select an option...'), 'id'=> 'type'])?>
    
    
    
    <div class="form-group">
        <?= $form->field($model, 'account_id')->widget(Select2::class,[
            'data' => ArrayHelper::map(Account::getOnlyAvailableForSelect(), 'account_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success', 'id'=>'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>