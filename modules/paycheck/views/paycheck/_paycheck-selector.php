<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
?>

<div id="paycheck-selector" style="display: none">
    
    <?= Html::label(Yii::t('paycheck','Paycheck')) ?>

    <div class="toggle-paycheck">
        <?php 
            $paycheckClass = get_class($model);
            $paycheckName = explode('\\', $paycheckClass);
            $paycheckEnd = end($paycheckName);
        ?>
        <input type="hidden" value="<?=(!$model->paycheck ? "" : $model->paycheck->paycheck_id )?>" id="paycheck_id" name="<?=$paycheckEnd?>[paycheck_id]"/>

        <div id="div-inputs" class="search" style="<?=(!$model->paycheck ? "" : "display: none;" )?>">
            <div class="input-group">
                <?= Html::textInput('paycheck_search', '', ['class'=>'form-control paycheck_search', 'id'=>'paycheck_search']) ?>
                <span class="input-group-btn">
                    <div class="btn btn-primary" onclick="SearchPaycheck.search( $('#paycheck_search').val() );">
                        <span class="glyphicon glyphicon-search"></span> <?= Yii::t('app', 'Search') ?>
                    </div>
                    <div onclick="SearchPaycheck.create(true);" class="btn btn-success">
                        <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('paycheck','Paycheck')]) ?>
                    </div>
                </span>
            </div>
        </div>


        <div id="div-remove" class="paycheck" style="<?=($model->paycheck ? "" : "display: none;" )?>">
            <div class="input-group">
                <?= Html::textInput('paycheck_data', ( !$model->paycheck ? "" : $model->paycheck->getFullDescription()), ['class'=>'form-control', 'disabled'=>'disabled', 'data-paycheck-id'=>($model->paycheck ? $model->paycheck->paycheck_id: "")]) ?>
                <span class="input-group-btn">
                    <div class="btn btn-warning remove-paycheck" onclick="SearchPaycheck.remove()"><span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app', 'Remove') ?></div>
                </span>
            </div>
        </div>

    </div>
</div>
<style>
    @media (min-width: 992px){
        .modal-lg
        {
            width: 900px;
        }
    }
</style>
<?php
//Modal
$modal = Modal::begin([
    'header' => '<h2>'. Yii::t('paycheck','Paycheck').'</h2>',
    'id'=>'modal-paycheck',
    'size'=>Modal::SIZE_LARGE,
    'class'=> 'modal',
    
    
]);
?>
<div id="modal-paycheck-body"><?= Yii::t('app', 'Loading...') ?></div>
<?php Modal::end(); ?>

<script>
    var SearchPaycheck = new function(){

        //private
        var autoFocus = false;
        var self = this;

        // Llamo a la interface de busqueda de cheques
        this.search = function(text, page){
            var data = new Object;
            data.text = text;

            //Pagination
            if(page){
                data.page = page;
            }
            this.createIframe('<?= \yii\helpers\Url::toRoute(['/paycheck/paycheck/encartera','embed'=>true, 'for_payment'=>$for_payment]) ?>');

        };

        // Llamo a la interface de creacion de Cheques
        this.create = function(from_thrid_party){
            if(from_thrid_party){
                this.createIframe('<?= yii\helpers\Url::toRoute(['/paycheck/paycheck/create', 'embed'=>true, 'for_payment'=>$for_payment, 'from_thrid_party' => true]) ?>');
            } else {
                this.createIframe('<?= yii\helpers\Url::toRoute(['/paycheck/paycheck/create', 'embed'=>true, 'for_payment'=>$for_payment]) ?>');
            }
        }

        this.toggleSearch = function(){
            $('.toggle-paycheck .search').toggle();
            $('.toggle-paycheck .paycheck').toggle();

        }

        // Creo el iframe
        this.createIframe = function (url) {
            var iframe = '<iframe style="width: 100%; height: 400px; border: 0;" border="0" padding:"0" src="'+url+'"></iframe>';
            $('#modal-paycheck-body').html(iframe);
            $('#modal-paycheck').modal('show');
        }
        
        /**this.createIframe = function (url) {
            $.ajax({
                url: url,
                method: 'POST',
                success: function (data) {
                    $('#modal-paycheck-body').html(data);
                    $('#modal-paycheck').modal('show');
                },
            });

        }**/

        // Selecciono el cheque y pongo los datos en la interface
        this.select = function(id){

            $('#modal-paycheck-body').html('Cargando...');
            $('#modal-paycheck').modal('hide');

            var data = new Object;
            data.id = id;

            $.ajax({
                url: '<?= \yii\helpers\Url::toRoute(['/paycheck/paycheck/select-paycheck']) ?>',
                data: data,
                dataType: 'json',
                type: 'get'
            }).done(function(json){
                if(json.status == 'success'){
                    $("[name=paycheck_data]").val(json.fullDescription);
                    $("#paycheck_id").val(json.paycheck.paycheck_id);
                    SearchPaycheck.toggleSearch();
                    SearchPaycheck.onSelect(json);
                }

            });

        }

        // Remuevo el cheque de la interface y habilito nuevamente la busqueda.
        this.remove = function(){
            SearchPaycheck.toggleSearch();
            $("[name=paycheck_data]").val("");
            $("#paycheck_id").val("");
            SearchPaycheck.onRemove();
        }

        this.onSelect = function(json){}
        this.onRemove = function(){}
    }
</script>