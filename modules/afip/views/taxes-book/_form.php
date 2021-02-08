<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="taxes-book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::hiddenInput('TaxesBook[type]', $model->type) ?>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <?= $form->field($model, 'period')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'MM-yyyy','options' => ['class' => 'form-control',],]) ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
