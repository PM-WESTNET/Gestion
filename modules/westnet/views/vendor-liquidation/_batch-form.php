<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\models\Vendor;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vendor-liquidation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'period')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Continue'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
