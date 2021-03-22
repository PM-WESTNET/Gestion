<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\InstructiveCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instructive-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([
            \app\modules\instructive\models\InstructiveCategory::STATUS_ENABLED => Yii::t('app','Enabled'),
            \app\modules\instructive\models\InstructiveCategory::STATUS_DISABLED => Yii::t('app','Disabled'),
    ]) ?>

    <?php echo $form->field($model, 'instructiveCategoryHasRoles')->widget(\kartik\select2\Select2::class, [
        'data' => $roles,
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'options' => ['multiple' => true]
    ])?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
