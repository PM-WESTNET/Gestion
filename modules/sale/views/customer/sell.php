<?php
use yii\widgets\ActiveForm;

$this->registerJs('Customer.init();');

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('app', 'Customer')]);
?>

<div class="page-header">
    <h1><?= $this->title ?></h1>
</div>

<div class="row">
    <div class="col-lg-6" id="customer-view">
        
    </div>
    <div class="col-lg-6">
        <a id="add-customer-btn" class="btn btn-success pull-right" href="<?= \yii\helpers\Url::to(['customer/create']) ?>"><span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'New Customer') ?></a>
    </div>
</div>

<?php $form = ActiveForm::begin(); ?>
    <div class="row" id="customer-search-row">
        <div class="col-sm-12 col-xs-12">
            <div class="help-block">
                <?= Yii::t('app', 'Search by customer name or document number.') ?>
            </div>
            <?php
            echo $this->render('_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id', 'label' => Yii::t('app','Search Customer')]);
            ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<script>
    
    var Customer = new function(){
        
        this.init = function(){
            $('#customersearch-customer_id').on('change', function(){
                if(!$(this).val()){
                    return;
                }
                
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['customer/view']) ?>',
                    data: {id: $(this).val()},
                    type: 'get',
                    beforeSend: function(){
                        $('#customer-search-row').css('opacity', 0.6);
                    }
                }).always(function(){
                    $('#customer-search-row').css('opacity', 1);
                }).done(function(json){
                    if(json.status == 'success'){
                        var template = $('#view-customer-template').html();
                        Mustache.parse(template);   // optional, speeds up future uses
                        $('#customer-view').html(Mustache.render(template,json.model));
                        
                        $('#add-customer-btn').removeClass('btn-success').addClass('btn-default');
                    }
                }).error(function(){
                    alert('Error.')
                });
            });
            
            $('body').on('click', '#customer-view a', function(){
                var url = $(this).attr('data-href');
                if(url.indexOf('?')){
                    url += '&';
                }else{
                    url += '?';
                }
                url += 'customer_id='+$(this).attr('data-customer-id');
                document.location = url;
            });
        }
        
    }
    
</script>


<script id="view-customer-template" type="x-tmpl-mustache">

<h3>{{name}} {{lastname}}</h3>
<p>{{documentType.name}} {{document_number}}</p>
    
<a data-href="<?= \yii\helpers\Url::to(['/sale/contract/contract/create']) ?>" data-customer-id="{{customer_id}}" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span><?= Yii::t('app', 'Create Contract') ?></a>

</script>