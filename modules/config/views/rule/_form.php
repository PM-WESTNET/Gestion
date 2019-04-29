<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Rule */
/* @var $form yii\widgets\ActiveForm */
$this->registerCss('.attr {display: none;}');
?>

<div class="rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'validator')->dropDownList(app\modules\config\models\Rule::getValidatorsList(), ['id' => 'validator', 'prompt' => ConfigModule::t('config', 'Select')]) ?>

    <?= $form->field($model, 'message')->textInput(['maxlength' => 255, 'placeholder' => ConfigModule::t('config', 'Optional')])->hint(ConfigModule::t('config', 'If not set, arya default messages would be used.')) ?>

    <?= $form->field($model, 'min', ['options' => ['class' => 'attr', 'id' => 'attr-min']])->textInput([]) ?>
    
    <?= $form->field($model, 'max', ['options' => ['class' => 'attr', 'id' => 'attr-max']])->textInput([]) ?>

    <?= $form->field($model, 'pattern', ['options' => ['class' => 'attr', 'id' => 'attr-pattern']])->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'format', ['options' => ['class' => 'attr', 'id' => 'attr-format']])->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'targetAttribute', ['options' => ['class' => 'attr', 'id' => 'attr-targetAttribute']])->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'targetClass', ['options' => ['class' => 'attr', 'id' => 'attr-targetClass']])->textInput(['maxlength' => 255]) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? ConfigModule::t('config', 'Create') : ConfigModule::t('config', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>

var rule = new function(){
    
    this.init = function(){
        $('#validator').on('change', function(){
            loadAttributes($(this).val());
        });
        if($('#validator').val()){
            loadAttributes($('#validator').val());
        }
    }
    
    function loadAttributes(validator){
        
        $('.attr').hide();
        
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['load-attributes']) ?>',
            type: 'post',
            data: {validator: validator},
            dataType: 'json'
        }).done(function(response){
            if(response.status == 'success'){
                showAttributes(response.attributes);
            }
        });
        
    }
    
    function showAttributes(attributes){
        attributes.forEach(function(element, index, array){
            $('#attr-'+element).show(50);
        })
    }
    
}

</script>

<?= $this->registerJs('rule.init()'); ?>