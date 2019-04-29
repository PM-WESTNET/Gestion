<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use app\modules\sale\models\BillType;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

\app\assets\ColumnUpdaterAsset::register($this);
/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 * @var yii\widgets\ActiveForm $form
 * @var yii\widgets\ActiveDataProvider $dataProvider details
 */
?>

<div class="bill-form">

    <div class="row hidden-print">
        <div class="col-sm-12" id="alerts">
        
        </div>
    </div>

    <?php 
    /**
     * CUIDADO: este form cierra luego de varios inputs que no corresponden al
     * modelo Bill.
     */
    $form = ActiveForm::begin(['id'=>'bill-form']); ?>
        <?= $this->render('_data-row', ['model' => $model, 'form' => $form, 'electronic_billing' => $electronic_billing]) ?>

        <hr class="hidden-print" />
        <div class="row">
            <div class="col-xs-12">
                <div class="input-group">
                    <span class="input-group-addon"><?= Yii::t('app','Search product') ?></span>

                    <?= Html::activeTextInput($productSearch, 'search_text',['class'=>'filter form-control search_text','id'=>'search_text']) ?>

                    <span class="btn input-group-addon" id="resetSearchBox">&times;</span>

                </div> 
                    <span class="hint-block" style="height: 30px;"><?= Yii::t('app','Please, bring the reader to the barcode or type any search text and press enter.') ?></span>
                    
                 
            </div>
        </div>

       <!--  <div class="row">
            <div class="col-xs-12">
            </div>
        </div>
 -->

        <?php \yii\widgets\Pjax::begin();



        //Vista de factura
        echo $this->render('_view', ['form'=>true,'model'=>$model,'detailsProvider'=>$dataProvider]);

        //Mostramos las operaciones solo si hay detalles agregados
        if($dataProvider->totalCount > 0 && !$model->hasErrors()): ?>

        <div class="row hidden-print">
            <div class="col-md-6">
                
                 <?php 
                //Si la factura ha sido modificada y se debe mostrar el boton "Cerrar" (params)
                if($model->formName() !== 'bill' || ($model->status != 'closed' && Yii::$app->params['bill_close_button']/* && $model->billType->multiplier !== 1*/)): ?>
                <a id="closeBill" class="btn btn-success <?= (!$model->customer_id) ? ' disabled' : '' ?>" href="<?= \yii\helpers\Url::toRoute(['bill/close','id'=>$model->bill_id]) ?>">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?= Yii::t('app','To Accept'); ?>
                </a>
                <?php endif; ?>
                
                    
                <?php 
                // Si el modulo pagos esta habilitado, mostramos el boton de pago. TODO: cambiar condicion == bill
                if(Yii::$app->getModule('checkout') && $model::$payable): ?>
                <a id="pay" class="btn btn-primary disabled" href="<?= \yii\helpers\Url::toRoute(['bill/close', 'id' => $model->bill_id, 'payAfterClose' => true]) ?>">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?= Yii::t('app','To Accept and Pay'); ?>
                </a>
                <?php endif; ?>    

            </div>
            <div class="col-md-6 text-right">
                <?php
                //Bye button
                //Solo se muestra si la factura ha sido modificada, y en funcion de la congiguracion en params
                $byeButton = Yii::$app->params['bill_bye_button'];
                if($byeButton['show']):
                ?>
                <a class="btn btn-default"  href="<?= \yii\helpers\Url::toRoute($byeButton['url']) ?>"><span class="glyphicon glyphicon-floppy-disk"></span> <?= $byeButton['label']; ?></a>
                <?php endif; ?>
            </div>

            <div class="col-md-12 text-right">
                <?= $this->render('_generator', ['model' => $model]) ?>
            </div>
        </div>

        <?php endif; ?>

        <hr/>
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group field-provider-account hidden-print">
                    <?=Html::label(Yii::t('app', "User"), ['user_id'])?>
                    <?= Select2::widget([
                        'model' => $model,
                        'attribute' => 'user_id',
                        'data' => yii\helpers\ArrayHelper::map(webvimark\modules\UserManagement\models\User::find()->all(), 'id', 'username' ),
                        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'data-update-bill' => '' ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
        
        <?php \yii\widgets\Pjax::end(); ?>
        
    <?php ActiveForm::end(); ?>
        
    <?php
    //Formulario de alta de detalle "a mano"
    echo $this->render('_handwrite-detail', ['model'=>$model]);
    ?>
            
    <script type="text/javascript">
    
        <?php //Formulario embebido? ?>
        var embed = <?= $embed ? 'true' : 'false'; ?>
    
        //Singleton
        var Search = new function(){

            //private
            var autoFocus = false;
            var self = this;

            this.init = function(){
                $(".search_text").focus();
                $(".search_text").on("focusout",function(){ Search.focusout(); });
                $(".search_text").on("focusin",function(){ Search.focusin(); });
                $("#resetSearchBox").on("click",function(){ Search.clear(); });
                
                $(window).on("keypress",function(e){ Search.windowKeypress(e); });
                $(window).on("keyup",function(e){ if(e.which == 27) { Search.clear(); } });
            }

            //public
            this.windowKeypress = function(e){

                if($(":focus").length == 0 && e.which > 20 && e.which < 127) {
                    
                    autoFocus = true;
                    
                    $(".search_text").val(String.fromCharCode( e.which ));
                    $(".search_text").focus();

                }else if(e.which == 13 && $(":focus").hasClass('search_text')){
                    
                    self.search($(":focus").val());
                    e.preventDefault();
                    
                }else if(e.which == 13 && $(":focus").hasClass('customer_search')){
                    
                    SearchCustomer.search($(":focus").val());
                    e.preventDefault();
                    
                }
                
            }
            
            //public
            this.iPlaceholder = function(){
                $(".search_text").attr("placeholder",$(".search_text").val());
                $(".search_text").val("");
            }
            
            //public
            this.focusin = function(){
                
                if(autoFocus == false){
                    $(".search-text-block").show(300);
                }

            }
            
            //public
            this.focusout = function(){
                
                autoFocus = false;
                $(".search-text-block").hide(300);
                
            }
            
            //public
            this.search = function(text, page){
                
                var data = new Object;
                data.text = text;
                
                //Pagination
                if(page){
                    data.page = page;
                }
                
                $.ajax({
                    url: '<?= \yii\helpers\Url::toRoute(['search-product','id'=>$model->bill_id]) ?>',
                    data: data,
                    dataType: 'json',
                    type: 'get'
                }).done(function(json){
                    
                    if(json.detail){
                        
                        $.pjax.reload({container: '#grid'});
                        
                    }else{
                        
                        var template = $('#product-template').html();
                        Mustache.parse(template);   // optional, speeds up future uses
                        var rendered = Mustache.render(template, json);
                        $('#modal-body').html(rendered);
                        $('.search_text').val(text);
                        $('#m1 h2').text('<?= Yii::t('app', 'Products') ?>');
                        $('#m1').modal('show');
                        
                    }
                    
                    //console.log(json);
                    
                });
                
            }

            this.clear = function(){

            }
                        
        }
        
        //Singleton
        var Bill = new function(){
            
            this.init = function(){
                checkPayable();
                $(".remove-customer").on("click",function(){ Bill.removeCustomer(); });
                
                $("[data-update-bill]").on("change",function(){ Bill.save(); });
                $("#<?= strtolower($model->formName()) ?>-company_id").on("change",function(){Bill.save();});
                
                $("#observation").on("blur",function(){
                    if($(this).data('changed')=='true') {
                        Bill.save();
                    }
                });
                $("#observation").on("keyup",function(event){
                    var keyCode = event.keyCode || event.which;
                    if (keyCode!=9 && keyCode != 13 && keyCode != 16) {
                        $(this).data('changed', 'true');
                    }
                });
                $("#handwrite-detail-add").on("click", function(){ Bill.addDetail(); } )

                $("#bill-number").focusout(function() {
                    Bill.save();
                });
            }
            
            //public
            this.addProduct = function(product_id){
                
                $('#m1').modal('hide');
                
                var data = new Object;
                data.product_id = product_id;
                
                $.ajax({
                    url: '<?= \yii\helpers\Url::toRoute(['add-product','id'=>$model->bill_id]) ?>',
                    data: data,
                    dataType: 'json',
                    type: 'get'
                }).done(function(json){
                    
                    if(json.detail){
                        //$.pjax.reload({container: '#grid'});
                        window.location.reload();

                    }else if(json.status == 'error'){
                        var errors;
                        if( typeof json.errors === 'string' ) {
                            errors = json.errors;
                        }else{
                            errors = json.errors.joint(';');
                        }   
                        Bill.showAlert("danger", errors, 3000);
                    }
                    
                });
                
            }
            
            //Permite agregar un detalle manual
            //public
            this.addDetail = function(){
                
                var $form = $('#handwrite-detail-form');
                var data = $form.serialize();
                
                //Importante: 
                //https://github.com/yiisoft/yii2/issues/5991 #7260
                //TODO: actualizar cdo este disponible
                $('#handwrite-detail-form .form-group').removeClass('has-error');
                $('#handwrite-detail-form .form-group .help-block').text('');
                
                console.log($form.attr('action'));
                
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
            
            //public
            this.selectCustomer = function(id){
                
            
                $('#modal-body').html('Cargando...');
                $('#m1').modal('hide');
                
                var data = new Object;
                data.customer_id = id;
                
                $.ajax({
                    url: '<?= \yii\helpers\Url::toRoute(['select-customer','id'=>$model->bill_id]) ?>',
                    data: data,
                    dataType: 'json',
                    type: 'get'
                }).done(function(json){
                    
                    if(json.status == 'success'){
                        window.location.reload();
                    }
                    
                });
                
            }
            
            //public
            this.removeCustomer = function(){
            
                $('#m1').modal('hide');
                
                $.ajax({
                    url: '<?= \yii\helpers\Url::toRoute(['remove-customer','id'=>$model->bill_id]) ?>',
                    dataType: 'json',
                    type: 'get'
                }).done(function(json){
                    
                    if(json.status == 'success'){
                        
                        SearchCustomer.toggleSearch();
                        $('.toggle-customer .customer').html('');
                        
                        checkPayable();
                        Bill.save();
                    }
                    
                });
                
            }
            
            //private
            function checkPayable(){
            
                var customerRequired = <?= $model->billType->customer_required ? 'true' : 'false' ?>;

                var customerSet = $('[data-customer-id]').attr('data-customer-id');
                
                if(customerRequired && !customerSet){

                    if ( $('input[name="BillDetail[qty]"]').length > 0 ) {
                        Bill.showAlert('danger', '<?= Yii::t('app', 'A customer must be selected.') ?>', 4000);
                    }

                    $('#pay').addClass('disabled');
                    
                }else{
                    
                    $('#pay').removeClass('disabled');
                    
                }
            }
            
            //public
            this.close = function(){
            
                $.ajax({
                    url: '<?= \yii\helpers\Url::toRoute(['bill/close','id'=>$model->bill_id,'ajax'=>true]) ?>',
                    dataType: 'json',
                    type: 'get'
                }).done(function(json){
                    
                    if(json.status == 'success'){
                        
                        this.onBillClosed();
                        
                    }
                    
                });
            
            }
            
            //public
            this.pay = function(){
            
                var customerRequired = <?= Yii::$app->params['customer_required'] ? 'true' : 'false' ?>;
                
                var customerSet = $('[data-customer-id]').attr('data-customer-id');
                
                if(customerRequired && customerSet){
                    
                    window.location = '<?= \yii\helpers\Url::toRoute(['/checkout/payment/pay-bill','bill'=>$model->bill_id]) ?>';
                    
                }else{
                    
                    this.showAlert('error', '<?= Yii::t('app', 'A customer must be selected.') ?>', 2000);
                    
                }
            
            }

            //public
            this.save = function(){
                
                $('#bill-form').submit();
                
            }
            
            //public
            this.generate = function(type){
                var data = {
                    type: type,
                    details: $('#grid').yiiGridView('getSelectedRows')
                }
                window.location.replace(getUrl("<?= \yii\helpers\Url::to(['bill/generate', 'id' => $model->bill_id]) ?>", data));

            }

            function getUrl(url, extraParameters) {
                var extraParametersEncoded = $.param(extraParameters);
                var seperator = url.indexOf('?') == -1 ? "?" : "&";

                return(url + seperator + extraParametersEncoded);
            }
            
            //Event
            this.onBillClosed = function(){
                
                $('#closeBill').hide(200,function(){$('#closeBill').remove();});
                $('#printBill').hide(200,function(){$('#printBill').remove();});
                
                if(embed){
                    parent.onBillClosed();
                }
                
            }
            
            //Event
            this.onBillPrinted = function(){
                
                $('#closeBill').hide(200,function(){$('#closeBill').remove();});
                $('#printBill').hide(200,function(){$('#printBill').remove();});
                
                if(embed){
                    parent.onBillPrinted();
                }else{
                    window.location = '<?= \yii\helpers\Url::toRoute('product/') ?>';
                }
                
            }
            
            //public
            this.showAlert = function(type, message, duration){
                
                var tid = Date.now();
                var alert = '<div id='+tid+' class="alert alert-'+type+' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'+message+'</div>';
                
                $('#alerts').append(alert);
                
                setTimeout(function(){
                    $('#'+tid).hide(500,function(){$('#'+tid).remove()});
                },duration);
                
            }
            
        }
        
    </script>
    
    <?php $this->registerJs('Bill.init();'); ?>
    <?php $this->registerJs('Search.init();'); ?>
    <?php $this->registerJs('ColumnUpdater.init();'); ?>
    
    <?php 
    //Si esta activado el "inteligent placeholder" del cuadro de busqueda en app->params
    if(Yii::$app->params['inteligent_placeholder']['product']['search_text'])
        $this->registerJs('$("#search_text").on("focusin",function(){Search.iPlaceholder()});'); 

    //Selecciona el texto al posicionar el cursor sobre un input de una columna InputColumn
    if(Yii::$app->params['auto_select_input_column'])
        $this->registerJs('$(".input-column").on("mouseup",function(e){$(this).select();});'); 
    ?>
    
    <?php //MUSTACHE TEMPLATES ?>
    <?= $this->render('templates/_mustache-product-template', ['productSearch'=>$productSearch]) ?>
    
    <?= $this->render('templates/_mustache-customer-template') ?>
    
    <?= $this->render('templates/_mustache-select-customer-template') ?>

</div>

<?php 
//Modal
$modal = Modal::begin([
    'header' => '<h2>ww'.Yii::t('app','Search product result').'</h2>',
    'id'=>'m1',
    'size'=>Modal::SIZE_LARGE
]);
?>
<div id="modal-body"><?= Yii::t('app', 'Loading...') ?></div>
<?php
Modal::end();

?>