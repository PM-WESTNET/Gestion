<?php

use app\modules\westnet\models\Node;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NodeChangeProcess */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="node-change-process-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=Html::hiddenInput('input_file', null, ['id'=>'file_update']); ?>
    <?= $form->field($model, 'file')->widget(FileInput::class, [
        'pluginOptions' => [
            'showPreview' => false,
            'showCaption' => true,
            'showRemove' => true,
            'showUpload' => false,
            'overwriteInitial' => true,
            'initialPreview'=>($model->file ? [$model->file] : false ),
        ]]); ?>

    <?= $form->field($model, 'node_id')->widget(Select2::class, [
            'data' => Node::getForSelect()
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    var ChangeNodeProcess = new function(){
        this.init = function() {
            $('#nodechangeprocess-file').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#nodechangeprocess-file').val()==0) {
                            $('#file_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#nodechangeprocess-file').on('filebrowse', function(event) {
                $('#file_update').val(1);
            });
            $('#nodechangeprocess-file').on('fileclear', function(event) {
                $('#file_update').val(1);
            });
            $('#nodechangeprocess-file').on('fileselectnone', function(event) {
                $('#file_update').val(0);
            });
        }
    }
</script>
<?php $this->registerJs('ChangeNodeProcess.init()') ?>
