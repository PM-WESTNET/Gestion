<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\Partner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <input name="PagomiscuentasFile[type]" value="payment" type="hidden"/>

    <?= app\components\companies\CompanySelector::widget(['model'=>$model]); ?>

    <div class="form-group">
        <?php
        echo $form->field($model, 'date')->widget(yii\jui\DatePicker::className(), [
            'language' => Yii::$app->language,
            'model' => $model,
            'attribute' => 'date',
            'dateFormat' => 'dd-MM-yyyy',
            'options'=>[
                'class'=>'form-control dates',
                'id' => 'from-date'
            ]
        ]);
        ?>
    </div>

    <div class="form-group">
        <?=Html::hiddenInput('file_update', null, ['id'=>'file_update']); ?>
        <?= $form->field($model, 'file')->widget(FileInput::classname(), [
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
                'overwriteInitial' => true,
                'initialPreview'=>($model->file ? [$model->file] : false ),
            ]]); ?>
    </div>
    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var ImportForm = new function(){
        this.init = function() {
            $('#pagomiscuentasfile-file').on('click', function(event) {
                document.body.onfocus = function() {
                    setTimeout(function(){
                        if ($('#pagomiscuentasfile-file').val()==0) {
                            $('#file_update').val(0);
                        }
                        document.body.onfocus = null;
                    }, 100);
                };
            });

            $('#pagomiscuentasfile-file').on('filebrowse', function(event) {
                $('#file_update').val(1);
            });
            $('#pagomiscuentasfile-file').on('fileclear', function(event) {
                $('#file_update').val(1);
            });
            $('#pagomiscuentasfile-file').on('fileselectnone', function(event) {
                $('#file_update').val(0);
            });
        }
    }
</script>
<?php $this->registerJs('ImportForm.init()') ?>