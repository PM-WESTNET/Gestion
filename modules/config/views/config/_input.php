<?php
use yii\helpers\Html;
use app\modules\config\ConfigModule;
use dosamigos\ckeditor\CKEditor;
?>

<div class="form-group<?php if($model->hasErrors()) echo ' has-error' ?>">
    <?php if($model->type == 'checkbox'): ?>
        <?= Html::checkbox($model->attr, $model->value, ['uncheck' => 0, 'label' => $model->label]); ?>
    <?php elseif($model->type == 'textarea'): ?>
        <?= Html::label($model->label, $model->attr, ['class' => 'control-label']); ?>
        <?= Html::textarea($model->attr, $model->value, ['class' => 'form-control', 'rows' => 5]); ?>
    <?php elseif($model->type == 'html'): ?>
        <?= Html::label($model->label, $model->attr, ['class' => 'control-label']); ?>
        <?=
        CKEditor::widget([
            'name' => $model->attr,
            'value' => $model->value,
            'preset' => 'custom',
            'options' => ['rows' => 5],
            'clientOptions' => [
                'toolbar' => [
                    ['FontSize'],
                    ['Bold', 'Italic', 'Underline', 'Strike'],
                    ['NumberedList', 'BulletedList'],
                ],
                'resize_enabled' => false,
                'removePlugins' => 'elementspath',
            ],
        ])
        ?>
    <?php else: ?>
        <?= Html::label($model->label, $model->attr, ['class' => 'control-label']); ?>
        <?= Html::input($model->type, $model->attr, $model->value, ['class' => 'form-control']); ?>

    <?php endif; ?>
    <?php if($model->description): ?>
    
        <div class="help-block"><?= $model->description ?></div>
    
    <?php endif; ?>
    <?= Html::error($model, 'value', ['class' => 'help-block']); ?>
</div>