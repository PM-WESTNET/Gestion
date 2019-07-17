<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerMessage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([
            \app\modules\sale\models\CustomerMessage::STATUS_ENABLED => Yii::t('app','Enabled'),
            \app\modules\sale\models\CustomerMessage::STATUS_DISABLED => Yii::t('app','Disabled')
    ]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6, 'id' => 'content']) ?>

    <div class="row">
        <div class="col-sm-3">
            <?= Yii::t('app', 'SMS Count') ?>: <span id="smsCount" class="label label-info"></span>
        </div>
        <div class="col-sm-3">
            <?= Yii::t('app', 'Avaible chars') ?>: <span id="smsLength" class="label label-info"></span>
        </div>
    </div>
    <br>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?php echo Yii::t('app','Available Fields')?></h5>
        </div>
        <div class="panel-body">
            <p style="font-weight: bold"><?php echo Yii::t('app','Use the following options for insert data in the message')?></p>
            <p>
                <?php foreach (\app\modules\sale\models\CustomerMessage::availablesFields() as $key => $field):?>
                    <?php echo Html::a($field['description'], '#', ['class' => 'reference btn btn-default btn-sm field-btn', 'data-ref' => '{'.$key.'}', 'data-length' => $field['length']])?>
                <?php endforeach;?>
            </p>
        </div>
    </div>


    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    
    var CustomerMessage = new function() {
        
        this.init= function () {
            $(document).on('click', '.field-btn', function (e) {
                e.preventDefault();
                CustomerMessage.insertField($(this));
            })
        };
        
        this.insertField = function (field) {
            var txtarea = document.getElementById('message');
            var text = $(field).data('template');
            if (!txtarea) {
                return;
            }

            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart === '0') ?
                "ff" : (document.selection ? "ie" : false));
            if (br === "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            } else if (br === "ff") {
                strPos = txtarea.selectionStart;
            }

            var front = (txtarea.value).substring(0, strPos);
            var back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;

            if (br == "ie") {
                txtarea.focus();
                var ieRange = document.selection.createRange();
                ieRange.moveStart('character', -txtarea.value.length);
                ieRange.moveStart('character', strPos);
                ieRange.moveEnd('character', 0);
                ieRange.select();
            } else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }

            txtarea.scrollTop = scrollPos;
        }
    }
    
    
</script>

<?php
$js = <<<JS
$.fn.extend({
    insertAtCaret: function(myValue) {
        console.log(myValue);
        if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
        }
        else if (document.getElementById('content').selectionStart || document.getElementById('content').selectionStart == '0') {
            var startPos = document.getElementById('content').selectionStart;
            var endPos = document.getElementById('content').selectionEnd;
            var scrollTop = document.getElementById('content').scrollTop;
            document.getElementById('content').value = document.getElementById('content').value.substring(0, startPos)+myValue+document.getElementById('content').value.substring(endPos,document.getElementById('content').value.length);
            document.getElementById('content').focus();
            document.getElementById('content').selectionStart = startPos + myValue.length;
            document.getElementById('content').selectionEnd = startPos + myValue.length;
            document.getElementById('content').scrollTop = scrollTop;
        } else {
            $('#content').val($('#content').val() + ' ' + myValue );
            this.focus();
        }
    }
});

(function($){
    $.fn.smsArea = function(options){

    var
    e = this,
    cutStrLength = 0,

    s = $.extend({

        cut: true,
        maxSmsNum: 3,
        interval: 400,

        counters: {
            message: $('#smsCount'),
            character: $('#smsLength')
        },

        lengths: {
            ascii: [160, 306, 459],
            unicode: [70, 134, 201]
        }
    }, options);


    e.keyup(function(){

        clearTimeout(this.timeout);
        this.timeout = setTimeout(function(){

            var
            smsType,
            smsLength = 0,
            smsCount = -1,
            charsLeft = 0,
            text = e.val(),
            isUnicode = false;
            
            //Reemplazo la cantidad de caracteres con la cantidad que me indica cada etiqueta y no con su valor real.
            text2 = e.val().replace('{customer_name}', 'x'.repeat($('[data-ref="{customer_name}"]').data('length')));
            text2 = text2.replace('{code}', 'x'.repeat($('[data-ref="{customer_name}"]').data('length')));
            text2 = text2.replace('{payment_code}', 'x'.repeat($('[data-ref="{customer_name}"]').data('length')));
            text2 = text2.replace('{debt}', 'x'.repeat($('[data-ref="{customer_name}"]').data('length')));

            for(var charPos = 0; charPos < text2.length; charPos++){
                switch(text[charPos]){
                    case "\\n": 
                    case "[":
                    case "]":
                    case "\\\\":
                    case "^":
                    case "{":
                    case "}":
                    case "|":
                    case "€":
                        smsLength += 2;
                    break;

                    default:
                        smsLength += 1;
                }


                if(text.charCodeAt(charPos) > 127 && text[charPos] != "€") isUnicode = true;
            }

            if(isUnicode){
                smsType = s.lengths.unicode;

            }else{
                smsType = s.lengths.ascii;
            }

            for(var sCount = 0; sCount < s.maxSmsNum; sCount++){

                cutStrLength = smsType[sCount];
                if(smsLength <= smsType[sCount]){

                    smsCount = sCount + 1;
                    charsLeft = smsType[sCount] - smsLength;
                    break
                }
            }

            if(s.cut) e.val(text.substring(0, cutStrLength));
            smsCount == -1 && (smsCount = s.maxSmsNum, charsLeft = 0);

            s.counters.message.html(smsCount);
            s.counters.character.html(charsLeft);
        
            if(charsLeft < 20 && charsLeft > 5){
                s.counters.character.removeClass().addClass('label label-warning');
            }else if(charsLeft <= 5) {
                s.counters.character.removeClass().addClass('label label-danger');
            }else {
                s.counters.character.removeClass().addClass('label label-info');
            }
        
            if(smsCount > 1){
                s.counters.message.removeClass().addClass('label label-danger');
            }else{
                s.counters.message.removeClass().addClass('label label-info');
            }

        }, s.interval)
    }).keyup();
    
    $(document).on('click', '.reference', function(){
        $('#content').insertAtCaret($(this).data('ref'));
    });
}}(jQuery));
JS;

$this->registerJs($js);
$this->registerJs('$("#content").smsArea();')?>

