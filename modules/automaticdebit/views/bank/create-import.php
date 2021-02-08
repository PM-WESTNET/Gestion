<?php
use yii\bootstrap\ActiveForm;
use app\components\companies\CompanySelector;
use kartik\select2\Select2;
use yii\helpers\Html;

$this->title = Yii::t('app','Create new import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Banks for Automatic Debit'), 'url' => ['/automaticdebit/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $import->bank->name, 'url' => ['/automaticdebit/bank/view', 'id' => $import->bank->bank_id]];

?>

<div class="create-import">

    <h1 class="title">
        <?= $this->title?>
    </h1>

    <?php $form = ActiveForm::begin()?>

    <?= CompanySelector::widget([
        'form' => $form,
        'model' => $import
    ])?>

    <?= $form->field($import,'money_box_account_id')->widget(Select2::class, [
        'data' => $moneyBoxAccount,
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'options' => ['placeholder' => Yii::t('app','Select an option')]
    ])?>

    <?= $form->field($import, 'fileUploaded')->fileInput(['class' => 'form-control'])?>

    <?= Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success'])?>

    <?php ActiveForm::end()?>

</div>


