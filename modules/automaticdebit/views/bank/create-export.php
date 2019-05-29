<?php

$this->title = Yii::t('app','Create new export for direct debit');
?>

<div class="create-export">

    <h1 class="title">
        <?php echo $this->title?>
    </h1>


    <?php $form = \yii\bootstrap\ActiveForm::begin()?>

    <?php echo \app\components\companies\CompanySelector::widget([
        'form' => $form,
        'model' => $model
    ])?>

    <?php echo $form->field($model, 'from_date')->widget(\kartik\widgets\DatePicker::class, [
        'pluginOptions' => [
            'autoclose'=> true,
            'format' => 'dd-M-yyyy'
        ]
    ])?>

    <?php echo $form->field($model, 'to_date')->widget(\kartik\widgets\DatePicker::class, [
        'pluginOptions' => [
            'autoclose'=> true,
            'format' => 'dd-M-yyyy'
        ]
    ])?>

    <?php echo $form->field($model, 'debit_date')->widget(\kartik\widgets\DatePicker::class, [
        'pluginOptions' => [
            'autoclose'=> true,
            'format' => 'dd-M-yyyy'
        ]
    ])?>

    <?php \yii\bootstrap\ActiveForm::end()?>

</div>


