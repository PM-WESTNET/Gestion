<?php

use app\modules\accounting\components\ClassFinder;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountConfig */
/* @var $form yii\widgets\ActiveForm */

$countables = ClassFinder::getInstance()->findCountables();
$movements = ClassFinder::getInstance()->findMovements();
$countables = array_combine($countables, $countables);
$movements = array_combine($movements, $movements);
?>

<div class="account-config-form">

    <?php $form = ActiveForm::begin(['id'=>'account-config-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'class')->widget(Select2::className(),[
        'data' => $countables,
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);
    ?>

    <br/>
    <?= $form->field($model, 'classMovement')->widget(Select2::className(),[
        'data' => $movements,
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);
    ?>
    <br/>
    <?php ActiveForm::end(); ?>

    <?php
        echo $this->render('_form-accounts', ['model'=>$model,'dataProvider'=>$dataProvider, 'aca'=>new \app\modules\accounting\models\AccountConfigHasAccount()]);
    ?>

    <div class="form-group col-lg-12">
        <a id="printBill" onclick="AccountConfig.save();" class="btn btn-primary"><?= Yii::t('app','Save'); ?></a>
    </div>


</div>
<script>
    var AccountConfig = new function(){
        this.init = function() {
            $(document).off("click", "#account-add").on("click", "#account-add", function(){
                AccountConfig.addAccount();
            });
        };
        this.save = function(){
            $("#account-config-form").submit();
        };

        this.addAccount= function (){

            var $form = $('#account-add-form');
            var data = $form.serialize();

            $.ajax({
                url: $form.attr('action'),
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){

                if(json.detail){

                    $.pjax.reload({container: '#grid'});

                }else{

                    //Importante:
                    //https://github.com/yiisoft/yii2/issues/5991 #7260
                    //TODO: actualizar cdo este disponible
                    for(error in json.errors){

                        $('.field-'+error).addClass('has-error');
                        $('.field-'+error+' .help-block').text(json.errors[error]);

                    }

                }

            });

        }
    };
</script>
<?php  $this->registerJs("AccountConfig.init();"); ?>