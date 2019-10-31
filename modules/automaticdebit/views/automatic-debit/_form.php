<?php

use app\modules\automaticdebit\models\AutomaticDebit;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\AutomaticDebit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="automatic-debit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $this->render('../../../sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id', 'label' => Yii::t('app','Customer')])?>

    <?= $form->field($model, 'bank_id')->dropDownList($banks) ?>

    <?= $form->field($model, 'cbu')->textInput(['maxlength' => 22]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        AutomaticDebit::ENABLED_STATUS => Yii::t('app','Active'),
        AutomaticDebit::DISABLED_STATUS => Yii::t('app','Inactive')
    ]) ?>

    <?= $form->field($model, 'customer_type')->dropDownList([
        'own' => Yii::t('app','Bank Customer'),
        'other' => Yii::t('app','Other Customer')
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
