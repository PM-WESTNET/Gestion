<?php

use app\modules\accounting\models\ConciliationItem;
use app\modules\accounting\models\Resume;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Dialog;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */

$this->title = (!$readOnly ? Yii::t('app', 'Update {modelClass}: ', ['modelClass' => Yii::t('accounting', 'Conciliation'),]) : "" ) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Conciliations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->conciliation_id]];
$this->params['breadcrumbs'][] =  (!$readOnly ? Yii::t('app', 'Update') : "");
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
        padding:0px;
    }
    .panel-heading select {
        color:black;
    }
</style>
<div class="conciliation-create">
    <input type="hidden" value="<?=$model->conciliation_id?>" name="conciliation_id" id="conciliation_id"/>

    <?php if ($model->moneyBoxAccount->moneyBox->hasUndefinedOperationType()):?>
        <div class="alert alert-warning">
            <?php echo Yii::t('accounting','Money Box has undefined code operations')?>
        </div>
    <?php endif;?>

    <div class="row">
        <div class="col-sm-10">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <?php \yii\widgets\Pjax::begin(['id'=>'w_header']);?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <strong>
                        <?=$model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number?>
                    </strong>
                    <?php if (!$readOnly) { ?>
                    <!-- <div class="pull-right" role="group"> -->
                        <?php if (!$model->moneyBoxAccount->moneyBox->hasUndefinedOperationType()):?>
                            <button type="button" id="btnClose" class="btn btn-success pull-right">
                                <span class="glyphicon glyphicon-ok"></span> <?= Yii::t('app', 'Ready');?>
                            </button>
                        <?php endif;?>
                    <!-- </div> -->
                    <?php } ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-1 text-center">
                            <strong><?= Yii::t('app', 'Date'); ?></strong>
                            <br/>
                            <?= $model->date ?>
                        </div>
                        <div class="col-sm-2 text-center">
                            <strong><?= Yii::t('accounting', 'Date From'); ?></strong>
                            <br/>
                            <?= $model->date_from ?>
                        </div>
                        <div class="col-sm-2 text-center">
                            <strong><?= Yii::t('accounting', 'Date To'); ?></strong>
                            <br/>
                            <?= $model->date_to ?>
                        </div>
                        <div class="col-sm-1 text-center">
                            <strong><?= Yii::t('app', 'Status'); ?></strong>
                            <br/>
                            <?= Yii::t('accounting', ucfirst($model->status)) ?>
                        </div>
                        <div class="col-sm-3 text-center">
                            <strong><?= Yii::t('accounting', 'Account Debit Balance'); ?></strong>
                            <br/>
                            <?= Yii::$app->formatter->asCurrency($totalAccountDebit) ?>
                        </div>
                        <div class="col-sm-3 text-center">
                            <strong><?= Yii::t('accounting', 'Account Credit Balance'); ?></strong>
                            <br/>
                            <?= Yii::$app->formatter->asCurrency($totalAccountCredit) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php \yii\widgets\Pjax::end();?>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-6" style="padding: 0px;">
            <div class="panel panel-primary" style="padding: 0px;">
                <div class="panel-heading text-center">
                    <strong>
                        <?=Yii::t('accounting', 'Account Movements')?>
                    </strong>
                    <?php if (false) { ?>
                        <!-- <div class="pull-right" role="group"> -->
                            <button type="button" data-type="btnDeconciliate" class="btn btn-warning btnDeconciliate pull-right">
                                <?=Yii::t('accounting', 'Deconciliate');?> <span class="glyphicon glyphicon-triangle-right"></span>
                            </button>
                        <!-- </div> -->
                    <?php } ?>
                </div>
                <div class="panel-body" style="overflow-y: scroll; height: 500px">
                    <?php echo $this->render('movements_search')?>
                    <div id="movements_grid">
                        <?php echo $this->render('_movements', ['readOnly' => $readOnly, 'movementsDataProvider' => $movementsDataProvider])?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6" style="padding: 0px;">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <strong>
                        <?= Yii::t('accounting', 'Summary'); ?>
                    </strong>
                    <?php if (!$readOnly) { ?>
                    <!-- <div class="pull-right" role="group"> -->
                        <?php if (!$model->moneyBoxAccount->moneyBox->hasUndefinedOperationType()):?>
                            <button type="button" data-type="btnConciliate" class="btn btn-success btnConciliate pull-right">
                                <span class="glyphicon glyphicon-triangle-left"></span> <?=Yii::t('accounting', 'Conciliate');?>
                            </button>
                        <?php endif;?>
                    <!-- </div> -->
                    <?php } ?>
                </div>
                <div class="panel-body" style="overflow-y: scroll;  height: 500px"">

                    <?php echo $this->render('_resume_items_search', ['operationTypes' => $operationTypes, 'resume_id' => $model->resume_id])?>

                    <div id="w_resume_items" style="text-align: center">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $cols = [];
    if(!$readOnly) {
        $cols[] = [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function($model, $key, $index, $column) {
                return ['value' => $model->conciliation_item_id];
            }
        ];
    }
    $cols = array_merge($cols, [[
        'header'=>Yii::t('app', 'Date'),
        'value' => function ($model) {
            return ($model->date ? Yii::$app->formatter->asDate($model->date, 'dd-MM-yyyy')  : '' );
        }
    ],[
        'header'=>Yii::t('app', 'Description'),
        'attribute'=>'description',
    ],[
        'header'=>Yii::t('app', 'Amount'),
        'value' => function ($model) {
            return Yii::$app->formatter->asCurrency($model->amount);
        }
    ]]);

    echo GridView::widget([
        //'layout'=> '{items}',
        'id'=> 'wConciliated',
        'caption' => Yii::t('accounting', 'Conciliated'),
        'dataProvider' => $conciliatedDataProvider,
        'columns' => $cols,
    ]);?>

</div>
<script>
    var Conciliate = new function() {
        this.init = function (){
            $(document).off('click', '.btnDeconciliate').on('click', '.btnDeconciliate', function(){
                Conciliate.deconciliate($(this).data('type'));
            });
            $(document).off('click', '.btnConciliate').on('click', '.btnConciliate', function(){
                Conciliate.conciliate($(this).data('type'));
            });
            $(document).off('change', '#resume_id').on('change', '#resume_id', function(){
                Conciliate.findResumeItems($(this).val());
            });
            $(document).off('click', '#btnClose').on('click', '#btnClose', function(){
                Conciliate.close();
            });

            $(document).on('click', '#btn-resume-search', function (e) {
               e.preventDefault();
               Conciliate.findResumeItems(true);
            });

            $(document).on('click', '#btn-movement-search', function (e) {
                e.preventDefault();
                Conciliate.findMovements();
            });

            Conciliate.findResumeItems();
        }

        this.findResumeItems = function(search = false) {
            var data= {};
            if (!search){
                data = {
                    ResumeSearch: {
                        resume_id: "<?php echo $model->resume_id?>"
                    }
                };
            }else {
                data= $('#resume_search_form').serializeArray();
            }

            $.ajax({
                url: "<?=Url::toRoute(['/accounting/conciliation/get-resume-items', 'readOnly'=> $readOnly ])?>",
                data: data,
                method: 'POST',
                dataType: 'html',
                beforeSend: function() {
                    $('#w_resume_items').html('<img src="<?php echo Url::to('@web').'/images/ajax-loader.gif'?>" alt="" width="5%" height="5%">')
                },
                success: function(data){
                    $("#w_resume_items").html(data);
                }
            });
        }

        this.findMovements = function() {
            var data= {};

            data= $('#movements_search_form').serializeArray();


            $.ajax({
                url: "<?=Url::toRoute(['/accounting/conciliation/get-account-movements', 'id' => $model->conciliation_id,'readOnly'=> $readOnly ])?>",
                data: data,
                method: 'POST',
                dataType: 'html',
                beforeSend: function() {
                    $('#movements_grid').html('<img src="<?php echo Url::to('@web').'/images/ajax-loader.gif'?>" alt="" width="5%" height="5%">')
                },
                success: function(data){
                    $("#movements_grid").html(data);
                }
            });
        }


        this.conciliate = function(type) {
            // Busco lo seleccionado de movimientos y de resumen
            var resume = $('#w_resume_items_debit').yiiGridView('getSelectedRows');
            var movement = $('#wDebit').yiiGridView('getSelectedRows');

            var data = {};


            // Si tengo quiero conciliar movimientos del resumen para los que no tengo
            // movimientos contables, consulto si los quiero crear.
            if ( movement.length == 0 && resume.lenth > 0) {
                if( confirm("<?=Yii::t('accounting', 'The items marked generate summary accounting transactions, are you sure?')?>") ) {
                    data.resumeItems = resume;
                }
            } else {
                data.resumeItems = resume;
                data.movementItems = movement;
            }

            var conciliation_id = $('#conciliation_id').val();
            $.ajax({
                url: '<?= Url::toRoute(['/accounting/conciliation/conciliar', 'conciliation_id'=>$model->conciliation_id ])?>',
                method: 'POST',
                dataType: 'json',
                data: data,
                success: function(data){
                    if (data.status!="success") {
                        alert("<?=Yii::t('app', 'This resource could not be conciliated.')?>");

                    } else {
                        $.pjax.reload({container: '#w0'});
                    }
                }
            });



        }

        this.deconciliate = function(type) {
            var data= {
                keys:   $('#wConciliated').yiiGridView('getSelectedRows')
            };

            if (data.keys.length > 0) {
                var conciliation_id = $('#conciliation_id').val();

                $.ajax({
                    url: '<?=Url::toRoute(['/accounting/conciliation/deconciliate' ])?>&conciliation_id='+conciliation_id,
                    method: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(data){
                        if (data.status!="success") {
                            if (data.message!="") {
                                alert("<?=Yii::t('app', 'This resource could not be deleted.')?>");
                            }

                        } else {
                            $.pjax.reload({container: '#w0'});
                        }
                    }
                });
            }
        }

        this.close = function() {
            if (confirm("<?=Yii::t('accounting', 'Closing the conciliation mark and generate new accounting moves, you sure?')?>")) {
                $.ajax({
                    url: '<?=Url::toRoute(['/accounting/conciliation/close', 'conciliation_id' => $model->conciliation_id ])?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if (data.status=="success") {
                            window.location = '<?= \yii\helpers\Url::toRoute(['/accounting/conciliation/index']) ?>';
                        } else {
                            alert(data.message);
                        }
                    }
                });
            }
        }
    }
</script>
<?php  $this->registerJs("Conciliate.init();"); ?>