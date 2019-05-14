<?php

use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCardFile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-card-file-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?= $form->field($model, 'file_name')->widget(FileInput::class, [
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
                'overwriteInitial' => true,
                'initialPreview'=>($model->file_name ? [$model->file_name] : false ),
            ]]); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
