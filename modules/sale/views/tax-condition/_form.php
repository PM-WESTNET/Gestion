<?php

use app\modules\sale\models\BillType;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\TaxCondition;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model TaxCondition */
/* @var $form ActiveForm */
?>

<div class="tax-condition-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>
    
    <?= $form->field($model, 'billTypes')->checkboxList( ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name' ), ['separator' => '<br/>'] ) ?>

    <?= $form->field($model, 'billTypesBuy')->checkboxList( ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name' ), ['separator' => '<br/>'] ) ?>
    
    <?= $form->field($model, '_documentTypes')->widget(Select2::classname(), [
                'language' => 'es',
                'data' => ArrayHelper::map(DocumentType::find()->all(), 'document_type_id', 'name'),
                'options' => [
                    'multiple' => true,
                    'placeholder' => Yii::t('app', 'Select an option...')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>

    <?= $form->field($model, 'exempt')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
