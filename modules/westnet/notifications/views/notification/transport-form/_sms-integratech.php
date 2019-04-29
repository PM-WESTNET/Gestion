<?php 
/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */

use app\modules\westnet\notifications\components\transports\SMSIntegratechTransport;

$dataLength = SMSIntegratechTransport::getMaxLengthReplacement();
?>

<?=
$form->field($model, 'content')->textarea(['id' => 'content']);
?>
<div class="row">
    <div class="col-sm-3">
        <?= Yii::t('app', 'References') ?>:
    </div>
    <div class="col-sm-9">
        <span class="reference label label-default" data-ref="@Nombre" id="lbl-nombre" data-length="<?=$dataLength['@Nombre']?>">@Nombre</span>
        <span class="reference label label-primary" data-ref="@Telefono1" id="lbl-telefono1" data-length="<?=$dataLength['@Telefono1']?>">@Telefono1</span>
        <span class="reference label label-success" data-ref="@Telefono2" id="lbl-telefono2" data-length="<?=$dataLength['@Telefono2']?>">@Telefono2</span>
        <span class="reference label label-info" data-ref="@Codigo" id="lbl-codigo" data-length="<?=$dataLength['@Codigo']?>"></span>
        <span class="reference label label-warning" data-ref="@CodigoDePago" id="lbl-codigo-de-pago" data-length="<?=$dataLength['@CodigoDePago']?>">@CodigoDePago</span>
        <span class="reference label label-danger" data-ref="@CodigoEmpresa" id="lbl-codigo-empresa" data-length="<?=$dataLength['@CodigoEmpresa']?>">@CodigoEmpresa</span>
        <span class="reference label label-default" data-ref="@FacturasAdeudadas" id="lbl-facturas-adeudadas" data-length="<?=$dataLength['@FacturasAdeudadas']?>">@FacturasAdeudadas</span>
        <span class="reference label label-primary" data-ref="@Saldo" id="lbl-saldo" data-length="<?=$dataLength['@Saldo']?>">@Saldo</span>
        <span class="reference label label-success" data-ref="@Estado"></span>
        <span class="reference label label-info" data-ref="@Categoria" id="lbl-categoria" data-length="<?=$dataLength['@Categoria']?>">@Categoria</span>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <?= Yii::t('app', 'SMS Count') ?>: <span id="smsCount" class="label label-info"></span>
    </div>
    <div class="col-sm-3">
        <?= Yii::t('app', 'Avaible chars') ?>: <span id="smsLength" class="label label-info"></span>
    </div>
</div>
<hr/>

<?php
$js = <<<JS
$.fn.extend({
    insertAtCaret: function(myValue) {
        if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
        }
        else if (this.selectionStart || this.selectionStart == '0') {
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            var scrollTop = this.scrollTop;
            this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
            this.focus();
            this.selectionStart = startPos + myValue.length;
            this.selectionEnd = startPos + myValue.length;
            this.scrollTop = scrollTop;
        } else {
            $(this).val($(this).val() + myValue );
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
            text2 = e.val().replace('@Nombre', 'x'.repeat($('#lbl-nombre').data('length')));
            text2 = text2.replace('@Telefono1', 'x'.repeat($('#lbl-telefono1').data('length')));
            text2 = text2.replace('@Telefono2', 'x'.repeat($('#lbl-telefono2').data('length')));
            text2 = text2.replace('@Codigo', 'x'.repeat($('#lbl-codigo').data('length')));
            text2 = text2.replace('@CodigoDePago', 'x'.repeat($('#lbl-codigo-de-pago').data('length')));
            text2 = text2.replace('@CodigoEmpresa', 'x'.repeat($('#lbl-codigo-empresa').data('length')));
            text2 = text2.replace('@FacturasAdeudadas', 'x'.repeat($('#lbl-facturas-adeudadas').data('length')));
            text2 = text2.replace('@Saldo', 'x'.repeat($('#lbl-saldo').data('length')));
            text2 = text2.replace('@Categoria', 'x'.repeat($('#lbl-categoria').data('length')));

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
$this->registerJs('$("#content").smsArea();')
?>