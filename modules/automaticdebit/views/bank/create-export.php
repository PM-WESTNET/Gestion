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
        'model' => $export
    ])?>

    <?php echo $form->field($export, 'from_date')->widget(\kartik\widgets\DatePicker::class, [
        'pluginOptions' => [
            'autoclose'=> true,
            'format' => 'dd-mm-yyyy'
        ]
    ])?>

    <?php echo $form->field($export, 'to_date')->widget(\kartik\widgets\DatePicker::class, [
        'pluginOptions' => [
            'autoclose'=> true,
            'format' => 'dd-mm-yyyy'
        ]
    ])?>

    <?php echo $form->field($export, 'debit_date')->widget(\kartik\widgets\DatePicker::class, [
        'pluginOptions' => [
            'autoclose'=> true,
            'format' => 'dd-mm-yyyy'
        ]
    ])?>

    <?php echo \yii\helpers\Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success'])?>

    <?php \yii\bootstrap\ActiveForm::end()?>

</div>


