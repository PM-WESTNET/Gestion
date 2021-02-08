<?php

use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conciliation-item-form">

    <?php $form = ActiveForm::begin([
        'id'=>'item-add-form',
        'action' => ['add-summary-item', 'conciliation_id' => $conciliation_id],
        'enableClientValidation' => false,
        'options' => ['data-pjax' => true, 'onsubmit'=> 'return false' ]]);
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'description')->textInput(['maxlength' => 150]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?= $form->field($model, 'account_id')->widget(Select2::className(),[
                    'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name' ),
                    'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'amount')->textInput() ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'reference')->textInput(['maxlength' => 45]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => 45]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::button(Yii::t('app', 'Create'), ['class' => 'btn btn-primary btnAddConciliationItem'] ) ?>
        <?= Html::button(Yii::t('app', 'Create And Continue'), ['class' => 'btn btn-primary btnAddConciliationItemAndContinue'] ) ?>
        <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-warning btnCancel'] ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var ConciliationItemForm = new function() {
        this.init = function (){

            $(document).off('click', '.btnAddConciliationItem')
                .on('click', '.btnAddConciliationItem', function(){
                ConciliationItemForm.submit(true);
            });
            $(document).off('click', '.btnAddConciliationItemAndContinue')
                .on('click', '.btnAddConciliationItemAndContinue', function(){
                ConciliationItemForm.submit();
                $("#item-add-form input[type='text']").first().focus();
            });
            $(document).off('click', '.btnCancel')
                .on('click', '.btnCancel', function(){
                    ConciliationItemForm.clearForm();
                    $('#dialogItem').dialog('close');
            });

            /*
            $(document).off('open', 'select#conciliationitem-account_id')
                    .on('open', 'select#conciliationitem-account_id', function(){console.log("a");
                self.$search.attr('tabindex', 0);
                setTimeout(function () { self.$search.focus(); }, 10);//add this line
            });*/

            $(document).off('keypress', $("#item-add-form input"))
                    .on('keypress', $("#item-add-form input"), function(event){
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    var self = event.target;
                    if (keycode == 13) {
                        var allInputs = $("#item-add-form input");
                        for (var i = 0; i < allInputs.length; i++) {
                            if (allInputs[i] == self) {
                                try {

                                    while ((allInputs[i]).name == (allInputs[i + 1]).name) {
                                        i++;
                                    }
                                } catch(e){}

                                if ((i + 1) < allInputs.length){
                                    /*if ($(allInputs[i + 1]).hasClass("select2-hidden-accessible")) {
                                        $(allInputs[i + 1]).select2('open');
                                    } else {*/
                                        $(allInputs[i + 1]).focus();
                                    //}

                                } else {
                                    $('.btnAddConciliationItemAndContinue').trigger('click');
                                }
                            }
                        }
                    }
                });

        }

        this.submit = function(close){
            var $form = $("#item-add-form");
            var data = $form.serialize();

            $.ajax({
                url: $form.attr('action'),
                data: data,
                dataType: 'json',
                type: 'post'
            }).done(function(json){

                if(json.status=='success'){
                    $.pjax.reload({container: '#w0'});

                    ConciliationItemForm.clearForm();

                    if (close) {
                        $('#dialogItem').dialog('close');
                    }
                }else{

                    //Importante:
                    //https://github.com/yiisoft/yii2/issues/5991 #7260
                    //TODO: actualizar cdo este disponible
                    for(error in json.errors){
                        $('.field-conciliationitem-'+error).addClass('has-error');
                        $('.field-conciliationitem-'+error+' .help-block').text(json.errors[error]);
                    }
                    $("#item-add-form input[type='text']").first().focus();
                }
            });
        }

        this.clearForm = function(){
            var $form = $("#item-add-form");
            $form.find('input[type="text"]').val('');
            $("#item-add-form .form-group").removeClass('has-error');
            $("#item-add-form .help-block").text('');
        }
    }
</script>
<?php  $this->registerJs("ConciliationItemForm.init();"); ?>