<?php

use app\modules\afip\models\TaxesBook;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\afip\models\TaxesBook*/

$this->title = Yii::t('afip', 'Book ' . ucfirst($model->type)) . " - " . Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy') . " - ". Yii::t('afip', 'Number') . " " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('afip', 'Book ' . ucfirst($model->type)), 'url' => [$model->type]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/afip/taxes-book/view', 'id' => $model->taxes_book_id]];
$this->params['breadcrumbs'][] =  (!($model->status==TaxesBook::STATE_CLOSED) ? Yii::t('app', 'Update') : "");
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
        font-size: 12px;
    }
    .panel-heading select {
        color:black;
    }
    #col_totals {
        right:0px;
    }
</style>
<div class="taxes-book-add-bills">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="title no-margin">
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '. Yii::t('app', 'Back'), ['view', 'id' => $model->taxes_book_id], ['class' => 'btn btn-default']) ?>
    </div>

    <div class="panel panel-primary">
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
                <div class="col-sm-2 text-center">
                    <strong><?= Yii::t('afip', 'Period'); ?></strong>
                    <br/>
                    <?= Yii::$app->getFormatter()->asDate($model->period, 'M/yyyy') ?>
                </div>
                <div class="col-sm-3 text-center">
                    <strong><?= Yii::t('afip', 'Number'); ?></strong>
                    <br/>
                    <?= $model->number ?>
                </div>
                <div class="col-sm-3 text-center">
                    <strong><?= Yii::t('app', 'Status'); ?></strong>
                    <br/>
                    <?= Yii::t('app', ucfirst($model->status)) ?>
                </div>
                <div class="col-sm-1 text-center">
                    <?php if ($model->can(TaxesBook::STATE_CLOSED)) { ?>
                        <div class="btn-group btn-group-xs pull-right" role="group">
                            <button type="button" id="btnClose" class="btn btn-warning"><?=Yii::t('app', 'Close');?></button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-9">
            <?php if($model->status != TaxesBook::STATE_CLOSED){ ?>
                <div class="row" id="col_bills">
                </div>
                <div class="row" id="col_employee_bills">
                </div>
            <?php } ?>
            <div class="row" id="col_bills_added">
            </div>
        </div>
        <div class="col-sm-3" id="col_totals" >
        </div>
    </div>
</div>
<script>
    var AddBuyBills = new function() {
        this.init = function (){

            $(document).off('click', '#btnClose').on('click', '#btnClose', function(){
                AddBuyBills.close();
            });

            $(document).off('click', '#w_bills input[type=checkbox]').on('click', '#w_bills input[type=checkbox]', function(){
                var ids = [];
                var me = this;
                var page = $('#col_bills .pagination li.active a').data('page');

                if ($(this).hasClass("select-on-check-all")) {
                    $('#w_bills input[type=checkbox]').each(function(){
                        if(me != this) {
                            ids.push($(this).val());
                        }
                        page = 0;
                    });
                } else {
                    ids.push($(this).val());
                     page = page +1 ;
                }

                (new Promise(function(resolve, reject){
                    AddBuyBills.add(ids, page, 'provider');
                    resolve(true);
                }));
            });

            $(document).off('click', '#w_employee_bills input[type=checkbox]').on('click', '#w_employee_bills input[type=checkbox]', function(){
                var ids = [];
                var me = this;
                var page = $('#col_bills .pagination li.active a').data('page');

                if ($(this).hasClass("select-on-check-all")) {
                    $('#w_employee_employee_bills input[type=checkbox]').each(function(){
                        if(me != this) {
                            ids.push($(this).val());
                        }
                        page = 0;
                    });
                } else {
                    ids.push($(this).val());
                    page = page +1 ;
                }

                (new Promise(function(resolve, reject){
                    AddBuyBills.add(ids, page, 'employee');
                    resolve(true);
                }));
            });

            $(document).off('click', '#w_bills_added input[type=checkbox]').on('click', '#w_bills_added input[type=checkbox]', function(){

                var ids = [];
                var providers= [];
                var employees = [];
                var me = this;
                var page = $('#col_bills-added .pagination li.active a').data('page');

                if ($(this).hasClass("select-on-check-all")) {
                    $('#w_bills_added input[type=checkbox]').each(function(){
                        if(me != this && $(this).data('type') === 'provider') {
                            providers.push($(this).val());
                        }

                        if(me != this && $(this).data('type') === 'employee') {
                            employees.push($(this).val());
                        }
                        page = 0;
                    });
                } else {
                    if($(me).data('type') === 'provider') {
                        providers.push($(me).val());
                    }

                    if($(me).data('type') === 'employee') {
                        employees.push($(this).val());
                    }
                    page = page +1 ;
                }

                (new Promise(function(resolve, reject){
                    AddBuyBills.remove(providers, employees, page);
                    resolve(true);
                }));
            });

            if (<?= ($model->status == TaxesBook::STATE_DRAFT ? "true" : "false" ) ?>){
                $(".glyphicon-print").closest("div").remove();
            } else {
                $(".glyphicon-print").closest("div").removeAttr("onclick");
                $(".glyphicon-print").closest("div").on("click", function() {
                    AddBuyBills.print();
                });
            }

            $(document).off("change", '#provider_id')
                .on("change", '#provider_id',function (e) {
                AddBuyBills.initialData();
            });

            AddBuyBills.initialData();

            $(window).on('scroll', function(){
                $('#col_totals').css('position', 'absolute');
                if($('#col_totals').offset().top < ($(window).scrollTop() + $('#main-menu').height()) ) {
                    $('#col_totals').css('top', ($(window).scrollTop() + $('#main-menu').height()));
                } else {
                    $('#col_totals').css('top', $("#col_bills").offset().top);
                }

            });

            $(document).off('click', '#col_bills .pagination li a').on('click', '#col_bills .pagination li a', function(evt){
                evt.preventDefault();
                var page = $(this).data('page');
                page = page +1 ;
                var url = '<?= Url::toRoute(['/afip/taxes-book/initial-data-buy', 'id' => $model->taxes_book_id]) ?>' +'&page-bills='+page+'&per-page=10';

                $.ajax({
                    url: url,
                    method: 'POST',
                    dataType: 'json',
                    success: function(data){
                        $('#col_bills').html(data.buy_bills);
                        $('#col_employee_bills').html(data.buy_employee_bills);
                        $('#col_bills_added').html(data.buy_bills_added);
                        $('#col_totals').html(data.total);
                    }
                });
            });

            $(document).off('click', '#col_bills_added .pagination li a').on('click', '#col_bills_added .pagination li a', function (evt) {
                evt.preventDefault();
                var page = $(this).data('page');
                page = page + 1;
                var url = '<?= Url::toRoute(['/afip/taxes-book/initial-data-buy', 'id' => $model->taxes_book_id]) ?>' + '&page-added=' + page + '&per-page=10';

                $.ajax({
                    url: url,
                    method: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        $('#col_bills').html(data.buy_bills);
                        $('#col_employee_bills').html(data.buy_employee_bills);
                        $('#col_bills_added').html(data.buy_bills_added);
                        $('#col_totals').html(data.total);
                    }
                });
            });
        };

        this.initialData = function () {
            $.ajax({
                url: '<?= Url::toRoute(['/afip/taxes-book/initial-data-buy', 'id' => $model->taxes_book_id]) ?>',
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#col_bills').html(data.buy_bills);
                    $('#col_employee_bills').html(data.buy_employee_bills);
                    $('#col_bills_added').html(data.buy_bills_added);
                    $('#col_totals').html(data.total);
                }
            });
        }

        this.add = function (value, page, type) {
            var data;
            if (type === 'provider') {
                data = {'provider_bill_id': value}
            }

            if (type === 'employee') {
                data = {'employee_bill_id': value}
            }

            $.ajax({
                url: '<?= Url::toRoute(['/afip/taxes-book/add-bill', 'id' => $model->taxes_book_id]) ?>'+'&page-bills='+page,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    $('#col_bills').html(data.buy_bills);
                    $('#col_employee_bills').html(data.buy_employee_bills);
                    $('#col_bills_added').html(data.buy_bills_added);
                    $('#col_totals').html(data.total);
                }
            });
        }

        this.remove = function (providers, employees, page) {
            var data;

            data = {
                'provider_bill_id': providers,
                'employee_bill_id': employees
            };

            $.ajax({
                url: '<?= Url::toRoute(['/afip/taxes-book/remove-bill', 'id' => $model->taxes_book_id]) ?>'+'&page-added='+page,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    $('#col_bills').html(data.buy_bills);
                    $('#col_bills_added').html(data.buy_bills_added);
                    $('#col_employee_bills').html(data.buy_employee_bills);
                    $('#col_totals').html(data.total);
                }
            });
        }

        this.close = function () {
            if (confirm("<?= Yii::t('afip', 'Close the book generates marks in bills where you can not go back, you sure?') ?>")) {
                $.ajax({
                    url: '<?= Url::toRoute(['/afip/taxes-book/close', 'id' => $model->taxes_book_id]) ?>',
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

        this.print = function () {
            window.open('<?= Url::toRoute(['/afip/taxes-book/print', 'id' => $model->taxes_book_id]) ?>');
        };
    };
</script>
<?php $this->registerJs("AddBuyBills.init();"); ?>