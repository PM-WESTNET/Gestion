<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\PlanFeature */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="plan-feature-form">

    <?php $form = ActiveForm::begin(); ?>

    <div id='parent_id' class="select_parent">
        <?= $form->field($model, 'parent_id')->dropdownList(yii\helpers\ArrayHelper::map(app\modules\sale\modules\contract\models\PlanFeature::find()->where('parent_id IS NULL')->all(), 'plan_feature_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...'],  ['class' => 'select_parent']) ?>
    </div>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <div id='type_data'<?php if(!empty($model->parent_id)){echo 'hidden';}?>>
        <?= $form->field($model, 'type')->dropDownList(['radiobutton' => 'Radiobutton', 'checkbox' => 'Checkbox', ], ['prompt' => '']) ?>
    </div>
    
    <?= $form->field($model, 'description')->label(Yii::t('app', 'Description'))->textarea(['rows' => 2]) ?>

    <?php // $form->field($model, 'products')->checkboxList(yii\helpers\ArrayHelper::map(app\modules\sale\models\Product::find()->all(), 'product_id', 'name'), ['encode'=>false, 'separator'=>'<br/>']) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    function selectType(){
        $('.select_parent').on('change',function(){
        var content=$('[name="PlanFeature[parent_id]"]').val();
        console.log(content);
            if(content!=""){
                 $('#type_data').hide();
            }
            else{
                 $('#type_data').show();
            }
        });
        $('.select_parent').on('load',function(){
        var content=$('[name="PlanFeature[parent_id]"]').val();
        console.log(content);
            if(content!=""){
                 $('#type_data').hide();
            }
            else{
                 $('#type_data').show();
            }
        });
    }
    
    
</script>

<?php
    $this->registerJs('selectType();');
?>
