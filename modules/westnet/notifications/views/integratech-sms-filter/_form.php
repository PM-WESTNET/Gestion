<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\ticket\models\Category;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\IntegratechSmsFilter */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integratech-sms-filter-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'word')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'action')->dropDownList([ 'Delete' => Yii::t('app','Delete'), 'Create Ticket' => Yii::t('app','Create Ticket')], ['id' => 'action']) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app', 'Enabled'), 'disabled' => Yii::t('app', 'Disabled')]) ?>

    <div id="category_id" class="hidden">
        <?php echo $form->field($model, 'category_id')->widget(Select2::className(),[
            'data' => yii\helpers\ArrayHelper::map(\app\modules\ticket\models\Category::getForSelect(), 'category_id', 'name' ),
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]);
        ?>
    </div>

    <?= $form->field($model, 'is_created_automaticaly')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var Filter = new function () {
        this.init = function () {
            $(document).on('change', '#action', function(){
                console.log($('#action').val());
               if($('#action').val() == 'Create Ticket'){
                   $('#category_id').removeClass('hidden');
               } else {
                   $('#category_id').addClass('hidden');
               }
            });
        }
    }
</script>
<?php $this->registerJs('Filter.init()');?>