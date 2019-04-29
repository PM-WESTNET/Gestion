<?php
use yii\helpers\Html;
?>

<script id="customer-template" type="x-tmpl-mustache">
        
    <div id="grid">
        <div class="input-group">
            <?= Html::textInput('customer_search', '', ['class'=>'form-control customer_search', 'id'=>'customer_search_modal']) ?>
            <span class="input-group-btn">
                <div class="btn btn-primary" onclick="SearchCustomer.search( $('#customer_search_modal').val() );"><span class="glyphicon glyphicon-search"></span> <?= Yii::t('app', 'Search') ?></div>
            </span>
        </div>
        <br/>
        <div class="table-responsive">
             <table class="table">
                <thead>
                    <tr>
                        <td><?= Yii::t('app', 'Id'); ?></td>
                        <td><?= Yii::t('app', 'Code'); ?></td>
                        <td><?= Yii::t('app', 'Name'); ?></td>
                        <td><?= Yii::t('app', 'Type'); ?></td>
                        <td><?= Yii::t('app', 'Tax identificaton'); ?></td>
                        <td></td>
                    </tr>
                </thead>
                {{#items}}
                <tr>
                    <td data-title="ID">{{customer_id}}</td>
                    <td data-title="Code">{{code}}</td>
                    <td data-title="Nombre">{{lastname}}, {{name}}</td>
                    <td data-title="Tipo">{{type}}</td>
                    <td data-title="Identificación">{{document_number}}</td>
                    <td  style="padding-right: 0;"><a class="btn btn-success" onclick="Bill.selectCustomer('{{customer_id}}')"><?= Yii::t('app','Select') ?></a></td>
                </tr>
                {{/items}}
                {{^items}}
                <tr>
                    <td colspan="5"><strong><?= Yii::t('yii','No results found.') ?></strong></td>
                </tr>
                {{/items}}
            </table>
        </div>

        <div style="text-align: center;">
            <nav>
                <ul class="pagination">
                    {{#pages}}
                        {{#active}}
                        <li class="active"><a href="#">{{{label}}}</a></li>
                        {{/active}}
                        {{^active}}
                        <li><a href="#" onclick="SearchCustomer.search($('#customer_search_modal').val(),{{page}})">{{{label}}}</a></li>
                        {{/active}}
                    {{/pages}}
                </ul>
            </nav>
        </div>
    </div>

</script>