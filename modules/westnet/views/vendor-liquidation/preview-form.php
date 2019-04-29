<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\models\Vendor;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('westnet', 'Vendor Liquidation Preview');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendors'), 'url' => ['vendor/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <h2><?= $model->vendor->fullName ?></h2>

    <div class="vendor-liquidation-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'period')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Continue'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    
</div>