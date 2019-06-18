<?php

$this->title = Yii::t('app','Create new import for direct debit');
?>

<div class="create-import">

    <h1 class="title">
        <?php echo $this->title?>
    </h1>


    <?php $form = \yii\bootstrap\ActiveForm::begin()?>

    <?php echo \app\components\companies\CompanySelector::widget([
        'form' => $form,
        'model' => $import
    ])?>

    <?php echo $form->field($import,'money_box_account_id')->widget(\kartik\select2\Select2::class, [
        'data' => $moneyBoxAccount,
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'options' => ['placeholder' => Yii::t('app','Select an option')]
    ])?>

    <?php echo $form->field($import, 'fileUploaded')->fileInput(['class' => 'form-control'])?>

    <?php echo \yii\helpers\Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success'])?>

    <?php \yii\bootstrap\ActiveForm::end()?>

</div>


