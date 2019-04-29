<?php
use dosamigos\tinymce\TinyMce;
use app\modules\westnet\notifications\components\helpers\LayoutHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'subject')->textInput(['maxlength' => 255]) ?>

<?=
$form->field($model, 'content')->widget(TinyMce::className(), [
    'options' => ['rows' => 6],
    'language' => 'es',
    'clientOptions' => [
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    ]
]);
?>

<?= $form->field($model, 'layout')->dropDownList(LayoutHelper::getLayouts()); ?>

<?php /*
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('app','Media'); ?></h3>
    </div>
    <div class="panel-body">
        <?= \app\modules\media\components\upload\UploadWidget::widget(['media' => $model->media]); ?>
    </div>
</div>
 * 
 */
?>