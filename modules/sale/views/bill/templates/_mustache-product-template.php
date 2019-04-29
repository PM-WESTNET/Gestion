<?php
use yii\helpers\Html;

if(!app\modules\config\models\Config::getValue('sale_products_list_view')):
?>

<script id="product-template" type="x-tmpl-mustache">
        
    <div id="grid">
        <div class="input-group">
            <span class="input-group-addon"><?= Yii::t('app','Search code') ?></span>
            <?= Html::activeTextInput($productSearch, 'search_text',['class'=>'filter form-control search_text', 'id'=>'search_text_modal']) ?>
            <span class="input-group-btn">
                <div class="btn btn-success" onclick="Search.search( $('#search_text_modal').val() );"><span class="glyphicon glyphicon-search"></span> <?= Yii::t('app', 'Search') ?></div>
            </span>
        </div> 
        <br/> 
        <table class="table" style="margin-top: 10px;">
            <thead>
                <tr>
                    <td><?= Yii::t('app', 'Id'); ?></td>
                    <td><?= Yii::t('app', 'Name'); ?></td>
                    <td><?= Yii::t('app', 'Price'); ?></td>
                    <td><?= Yii::t('app', 'Final price'); ?></td>
                    <td><?= Yii::t('app', 'Stock Balance'); ?></td>
                    <td></td>
                </tr>
            </thead>
            {{#items}}
            <tr>
                <td>{{product_id}}</td>
                <td>{{name}}</td>
                <td>$ {{netPrice}}</td>
                <td>$ {{finalPrice}}</td>
                <td>{{avaible_combined_stock}}</td>

                <td><a class="btn btn-primary" onclick="Bill.addProduct('{{product_id}}')"><?= Yii::t('app','Add') ?></a></td>

            </tr>
            {{/items}}
            {{^items}}
            <tr>
                <td colspan="5"><strong><?= Yii::t('app','No results found. Disabled products?') ?></strong></td>
            </tr>
            {{/items}}
    </table>
    <div style="text-align: center;">
        <nav>
            <ul class="pagination">
                {{#pages}}
                    {{#active}}
                    <li class="active"><a href="#">{{{label}}}</a></li>
                    {{/active}}
                    {{^active}}
                    <li><a href="#" onclick="Search.search($('#search_text_modal').val(),{{page}})">{{{label}}}</a></li>
                    {{/active}}
                {{/pages}}
            </ul>
        </nav>
    </div>

</script>

<?php else: ?>

<script id="product-template" type="x-tmpl-mustache">
    
<div class="input-group">
    <span class="input-group-addon"><?= Yii::t('app','Search code') ?></span>
    <?= Html::activeTextInput($productSearch, 'search_text',['class'=>'filter form-control search_text', 'id'=>'search_text_modal']) ?>
    <span class="input-group-btn">
        <div class="btn btn-primary" onclick="Search.search( $('#search_text_modal').val() );"><span class="glyphicon glyphicon-search"></span> <?= Yii::t('app', 'Search') ?></div>
    </span>
</div> 

<br/>

{{#items}}
<div class="row  product-chart">
    
    <div class="col-xs-12 col-sm-3 no-padding">
        <div class="img-container">
             {{#poster}}
            <img style="width: 100%;" src="{{poster.thumbnail}}" />
            {{/poster}}
        </div>
    </div>

    <div class="col-xs-12 col-sm-9">
        <div class="row">
            
            <div class="col-xs-12 col-sm-12">
                <h5><?= Yii::t('app', 'Product'); ?></h5>
                <h4><span class="font-m font-bold font-light-gray"># {{product_id}}</span>  {{name}}</h5>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h5><?= Yii::t('app', 'Stock Balance'); ?></h5>
                <h4>{{avaible_combined_stock}}</h4>
            </div>        
        </div>

        <div class="row">

            <div class="col-xs-12 col-sm-6">
                <h5><?= Yii::t('app', 'Price'); ?></h5>
                <h4>$ {{netPrice}}</h4>
            </div>
            <div class="col-xs-12 col-sm-6">
                <h5><?= Yii::t('app', 'Final Price'); ?></h5>
                <h4 class="font-bold font-l">$ {{finalPrice}}</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                    <a class="btn btn-success pull-right" onclick="Bill.addProduct('{{product_id}}')"><span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app','Add') ?></a>
            </div>
        </div>
    </div>
</div>
{{/items}}

<div style="text-align: center;">
    <nav>
        <ul class="pagination">
            {{#pages}}
                {{#active}}
                <li class="active"><a href="#">{{{label}}}</a></li>
                {{/active}}
                {{^active}}
                <li><a href="#" onclick="Search.search($('#search_text_modal').val(),{{page}})">{{{label}}}</a></li>
                {{/active}}
            {{/pages}}
        </ul>
    </nav>
</div>

</script>

<?php endif; ?>
