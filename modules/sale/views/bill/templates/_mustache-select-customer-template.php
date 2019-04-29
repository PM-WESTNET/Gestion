<?php
use yii\helpers\Html;
?>

<script id="select-customer-template" type="x-tmpl-mustache">

    <div class="input-group">
        <input disabled class="form-control" data-customer-id="{{customer.customer_id}}" value="{{customer.name}} {{customer.lastname}} {{^customer.document_number}} ({{customer.document_number}}) {{/customer.document_number}}">
        <span class="input-group-btn" onclick="Bill.removeCustomer();">
            <div class="btn btn-warning remove-customer"><span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app', 'Remove') ?></div>
        </span>
    </div>


</script>