<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\models\Vendor;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorLiquidation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vendor-liquidation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if($model->isNewRecord): ?>
        <?php
        $vendors = Vendor::find()->orderBy(['lastname' => SORT_ASC, 'name' => SORT_ASC])->all();

        $select = [];
        foreach($vendors as $vendor){
            $select[$vendor->vendor_id] = "$vendor->lastname, $vendor->name";
        }

        echo $form->field($model, 'vendor_id')->dropDownList($select, ['prompt' => '']) ?>

        <?= $form->field($model, 'period')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?php else: ?>
    
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'vendor_liquidation_id',
                [
                    'attribute' => 'vendor_id',
                    'value' => $model->vendor->fullName
                ],
                [
                    'attribute' => 'periodMonth',
                    'format' => 'raw',
                    'value' => $model->period ? $model->periodMonth : Yii::$app->formatter->asDate($model->date, 'MM-yyyy')
                ],
                'date',
                [
                    'attribute' => 'total',
                    'format' => 'currency',
                ]
            ],
        ]) ?>
    
        <?= $form->field($model, 'status')->dropDownList(['draft' => Yii::t('app', 'Draft'), 'payed' => Yii::t('app', 'Payed')]) ?>
    
    <?php endif; ?>
    
    <div class="form-group">

        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
