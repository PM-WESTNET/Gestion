<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\instructive\models\Instructive */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instructive-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'summary')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'instructive_category_id')->widget(\kartik\select2\Select2::class, [
        'data' => $instructiveCategories,
        'pluginOptions' => [
            'allowClear' => true
        ],
        'options' => ['placeholder' => Yii::t('app','Select an Option')]
    ]) ?>

    <?= $form->field($model, 'content')->widget(\dosamigos\ckeditor\CKEditor::class, [
        'options' => ['rows' => 6],
        'preset' => 'standard',
        'clientOptions' => [
            'filebrowserImageUploadUrl' => 'kcfinder-master/upload.php?command=QuickUpload&type=Images',
            'access' => [
                'files' => [
                   'upload' => true
                ]
            ]
        ]

    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
