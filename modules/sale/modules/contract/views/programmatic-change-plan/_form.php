<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammaticChangePlan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="programmatic-change-plan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if(!empty($model->contract_id)):?>
        <h4><?php echo Yii::t('app','Customer')?> : <?php echo $customer->fullName . ' ('. $customer->code .')'?></h4>
        <?= $form->field($model, 'contract_id')->hiddenInput()->label(null) ?>
    <?php else:?>
        <?php echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['model' => $model, 'attribute' => 'customer_id', 'form' => $form])?>
    <?php endif;?>

    <?= $form->field($model, 'date')->widget(\kartik\date\DatePicker::class, [
            'pluginOptions' => ['format' => 'dd-mm-yyyy']
    ]) ?>


    <?= $form->field($model, 'product_id')->widget(\kartik\select2\Select2::class, [
        'data' => $planes,
        'options' => ['placeholder' => Yii::t('app','Select a plan')]
    ]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
