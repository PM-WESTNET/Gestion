<?php

use app\modules\afip\models\TaxesBook;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */

$this->title = Yii::t('afip', 'Book ' . ucfirst($model->type)) . " - " . Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy') . " - ". Yii::t('afip', 'Number') . " " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('afip', 'Book ' . ucfirst($model->type)), 'url' => [$model->type]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/afip/taxes-book/view', 'id' => $model->taxes_book_id]];
$this->params['breadcrumbs'][] =  (!($model->status==TaxesBook::STATE_CLOSED) ? Yii::t('app', 'Update') : "");
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
        padding:5px;
        font-size: 13px;
    }
</style>
<div class="taxes-book-add-bills">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="title no-margin">
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '. Yii::t('app', 'Back'), ['view', 'id' => $model->taxes_book_id], ['class' => 'btn btn-default']) ?>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>

            </strong>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3 text-center">
                    <strong><?= Yii::t('app', 'Company'); ?></strong>
                    <br/>
                    <?= $model->company->name ?>
                </div>
                <div class="col-sm-1 text-center">
                    <strong><?= Yii::t('afip', 'Period'); ?></strong>
                    <br/>
                    <?= Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy') ?>
                </div>
                <div class="col-sm-2 text-center">
                    <strong><?= Yii::t('afip', 'Number'); ?></strong>
                    <br/>
                    <?= $model->number ?>
                </div>
                <div class="col-sm-2 text-center">
                    <strong><?= Yii::t('app', 'Status'); ?></strong>
                    <br/>
                    <?= Yii::t('app', ucfirst($model->status)) ?>
                </div>
                <div class="col-sm-1 text-center">
                    <?php if ($model->can(TaxesBook::STATE_CLOSED)) { ?>
                        <div class="btn-group btn-group-xs pull-right" role="group">
                            <button id="btnClose" class="btn btn-warning" autocomplete="off" data-loading-text="<?php echo Yii::t('app', 'Processing')?>"><?=Yii::t('app', 'Close');?></button>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-sm-3 text-center">
                    <?php if ($model->status != TaxesBook::STATE_CLOSED) { ?>
                        <div class="btn-group btn-group-xs pull-right" role="group">
                            <button type="button" id="btnSave" class="btn btn-success"><?=Yii::t('afip', 'Save temporarily');?></button>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
    <div class="row">
        <?php \yii\widgets\Pjax::begin(['id'=>'w_bills']);?>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <?=Yii::t('app', 'Bills')?>
                    </strong>
                </div>
                <div class="panel-body">
                    <?php
                     if (!$model->getTaxesBookItems()->exists()) {
                        $columns = [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'header'=>Yii::t('app','Customer'),
                                'value'=>function($model){ if(!empty($model->customer)) return $model->customer->fullName; },
                                'enableSorting' => false
                            ],
                            [
                                'attribute' => 'bill_type_id',
                                'label' => Yii::t('app', 'Type'),
                                'value' => function($model){
                                    return $model->billType ? $model->billType->name : null;
                                },
                                'enableSorting' => false
                            ],
                            [
                                'attribute' => 'number',
                                'value' => function($model){
                                    return $model->numberFromPointOfSale.'-'.$model->number;
                                },
                                'enableSorting' => false
                            ],
                            [
                                'attribute' => 'date',
                                'enableSorting' => false,
                                'format' => 'date',
                                'footer' => '<strong>Totales</strong>',
                            ],
                            [
                                'attribute'=>'amount',
                                'format'=>['currency'],
                                'enableSorting' => false,
                                'footer' => '<strong>' . Yii::$app->formatter->asCurrency($totals['amount']) . '</strong>',
                            ],
                            [
                                'attribute'=>'taxes',
                                'format'=>['currency'],
                                'enableSorting' => false,
                                'footer' => '<strong>' . Yii::$app->formatter->asCurrency($totals['taxes']) . '</strong>',
                            ],
                            [
                                'attribute'=>'total',
                                'format'=>['currency'],
                                'enableSorting' => false,
                                'footer' => '<strong>' . Yii::$app->formatter->asCurrency($totals['total']) . '</strong>',
                            ],
                        ];

                     } else {
                        $columns = [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'header'=>Yii::t('app','Customer'),
                                'value'=>function($model){ if(!empty($model->bill->customer)) return $model->bill->customer->fullName; },
                                'enableSorting' => false
                            ],
                            [
                                'attribute' => 'bill_type_id',
                                'label' => Yii::t('app', 'Type'),
                                'value' => function($model){
                                    return ($model->bill->billType ? $model->bill->billType->name : null);
                                },
                                'enableSorting' => false
                            ],
                            [
                                'attribute' => 'number',
                                'value' => function($model){
                                    return $model->bill->numberFromPointOfSale.'-'.$model->bill->number;
                                },
                                'enableSorting' => false
                            ],
                            [
                                'attribute' => 'bill.date',
                                'enableSorting' => false,
                                'format' => 'date',
                                'footer' => '<strong>Totales</strong>',
                            ],
                            [
                                'attribute'=>'bill.amount',
                                'format'=>['currency'],
                                'enableSorting' => false,
                                'footer' => '<strong>' . Yii::$app->formatter->asCurrency($totals['amount']) . '</strong>',
                            ],
                            [
                                'attribute'=>'bill.taxes',
                                'format'=>['currency'],
                                'enableSorting' => false,
                                'footer' => '<strong>' . Yii::$app->formatter->asCurrency($totals['taxes']) . '</strong>',
                            ],
                            [
                                'attribute'=>'bill.total',
                                'format'=>['currency'],
                                'enableSorting' => false,
                                'footer' => '<strong>' . Yii::$app->formatter->asCurrency($totals['total']) . '</strong>',
                            ],
                        ];
                     }
                     // saco los orders
                     echo GridView::widget([
                        'id'=> 'wBills',
                        'caption' => Yii::t('afip', 'All sale bills in the period selected are added to the book.'),
                        'dataProvider' => $dataProvider,
                        'columns' => $columns,
                        'showFooter' => true,
                    ]);?>

                </div>
            </div>
        </div>
        <?php \yii\widgets\Pjax::end();?>
    </div>
</div>
<script>
    var AddSaleBills = new function() {
        this.init = function (){
            $(document).off('click', '#btnClose').on('click', '#btnClose', function(){
                AddSaleBills.close();
            });

            $(document).off('click', '#btnSave').on('click', '#btnSave', function(){
                AddSaleBills.save();
            });

            if (<?=($model->status==TaxesBook::STATE_DRAFT ? "true" : "false" )?>){
                $(".glyphicon-print").closest("div").remove();
            } else {
                $(".glyphicon-print").closest("div").removeAttr("onclick");
                $(".glyphicon-print").closest("div").on("click", function() {
                    AddSaleBills.print();
                });
            }
        }

        this.save = function () {
            if (confirm("<?= Yii::t('afip', 'Are you sure you want to save te items') ?>")) {
                $.ajax({
                    url: '<?= Url::toRoute(['/afip/taxes-book/save', 'id' => $model->taxes_book_id]) ?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        if (data.status != "success") {
                            if (data.message != "") {
                                alert(data.message);
                            }
                        } else {
                            window.location = '<?= \yii\helpers\Url::toRoute(['/afip/taxes-book/' . $model->type]) ?>';
                        }
                    }
                });
            }
        };

        this.close = function() {
            if (confirm("<?= Yii::t('afip', 'Close the book generates marks in bills where you can not go back, you sure?')?>")) {
                $('#btnClose').button('loading');
                $.ajax({
                    url: '<?=Url::toRoute(['/afip/taxes-book/close', 'id' => $model->taxes_book_id])?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if (data.status!="success") {
                            if (data.message!="") {
                                alert(data.message);
                            }
                            $('#btnClose').button('reset');
                        } else {
                            window.location = '<?= \yii\helpers\Url::toRoute(['/afip/taxes-book/'. $model->type]) ?>';
                        }
                    }
                });
            }
        }

        this.print = function() {
            window.open('<?=Url::toRoute(['/afip/taxes-book/print', 'id'=>$model->taxes_book_id])?>');
        }
    }
</script>
<?php  $this->registerJs("AddSaleBills.init();"); ?>