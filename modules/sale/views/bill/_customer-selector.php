<?php 
use yii\helpers\Html;
?>

<?= Html::label(Yii::t('app','Customer')) ?>
            
<div class="toggle-customer">
    
    <?php if(empty($model->customer)): ?>

    <div class="search row">

        <!-- <div class=""> -->
            <div class="col-md-5 no-padding">
                <?= Html::textInput('customer_search', '', ['class'=>'form-control customer_search', 'id'=>'customer_search']) ?>
            </div>

            <div class="col-md-7">
                <a class="btn btn-primary" onclick="SearchCustomer.search( $('#customer_search').val() );"><span class="glyphicon glyphicon-search"></span></a>
                <span>
                    &nbsp;
                </span>
                <a onclick="SearchCustomer.create();" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app','Customer')]) ?></a>
            </div>                


    </div>

    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="customer" style="display: none;">

                <div class="input-group">
                    <?= Html::textInput('customer_data', '', ['class'=>'form-control', 'disabled'=>'disabled']) ?>
                    <span class="input-group-btn">
                        <a class="btn btn-warning remove-cuswatomer"><span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app', 'Remove') ?></a>
                    </span>
                </div>

            </div>
        </div>
    </div>
    
    <?php else: ?>

    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="search" style="display: none;">

                <div class="input-group">
                    <?= Html::textInput('customer_search', '', ['class'=>'form-control customer_search', 'id'=>'customer_search']) ?>
                    <span class="input-group-btn">
                        <a class="btn btn-primary" onclick="SearchCustomer.search( $('#customer_search').val() );"><span class="glyphicon glyphicon-search"></span></a>
                        <span>&nbsp;</span>
                        <a onclick="SearchCustomer.create();" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'Create {modelClass}', ['modelClass'=>Yii::t('app','Customer')]) ?></a>

                    </span>
                </div>

            </div>
            
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="customer">

                <div class="input-group">
                    <?= Html::textInput('customer_data', $model->customer->fullName, ['class'=>'form-control', 'disabled'=>'disabled', 'data-customer-id'=>$model->customer->customer_id]) ?>
                    <span class="input-group-btn  padding-left-half">
                        <a class="btn btn-warning remove-customer"><span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app', 'Remove') ?></a>
                    </span>
                </div>

            </div>
            
        </div>
    </div>
    
    <?php endif; ?>
</div>

<script>
    
    //Singleton
    var SearchCustomer = new function(){

        //private
        var autoFocus = false;
        var self = this;

        this.search = function(text, page){

            var data = new Object;
            data.text = text;

            //Pagination
            if(page){
                data.page = page;
            }

            $.ajax({
                url: '<?= \yii\helpers\Url::toRoute(['search-customer','id'=>$model->bill_id]) ?>',
                data: data,
                dataType: 'json',
                type: 'get'
            }).done(function(json){

                if(json.status == 'success'){

                    var template = $('#customer-template').html();
                    Mustache.parse(template);   // optional, speeds up future uses
                    var rendered = Mustache.render(template, json);
                    $('#modal-body').html(rendered);
                    $('.search_customer').val(text);
                    $('#m1 h2').text('<?= Yii::t('app', 'Customers') ?>');
                    $('#customer_search_modal').val(json.text);
                    $('#m1').modal('show');

                }

                //console.log(json);

            });

        }

        //public
        this.toggleSearch = function(){

            $('.toggle-customer .search').toggle();
            $('.toggle-customer .customer').toggle();

        }

        //public
        this.create = function(){

            var iframe = '<iframe name="customer-iframe" style="width: 100%; height: 400px; border: 0;" border="0" src="<?= yii\helpers\Url::to(['customer/create-embed','Customer[company_id]'=>$model->company_id]) ?>"></iframe>';
            $('#modal-body').html(iframe);
            $('#m1 h2').text('<?= Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Customer')]) ?>');
            $('#m1').modal('show');

        }

    }
    
</script>