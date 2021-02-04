<?php
use app\modules\sale\models\Customer;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\Html;

$customer_id = ($attribute ? $attribute : 'customer_id' );
$customer = Customer::findOne($model->$customer_id);
$name = empty($model->$customer_id) ? '' : $customer->lastname.', '.$customer->name;

//Plugin options
$pluginOptions = [
    'allowClear' => true,
    'minimumInputLength' => 3,
    'ajax' => [
        'url' => Url::to(['/sale/customer/find-by-name']),
        'dataType' => 'json',
        'data' => new JsExpression('function(params) { return {name:params.term, id:$(this).val()}; }')
    ],
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    'templateResult' => new JsExpression('function(customer) { return customer.text; }'),
    'templateSelection' => new JsExpression('function (customer) { return customer.text; }'),
    'cache' => true
];

//Con ActiveForm
if(isset($form)){
    echo $form->field($model, $customer_id)->label(isset($label) ? $label : null)->widget(Select2::classname(), [
        'initValueText' => $name,
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
    
//Sin ActiveForm
}else{
    echo Select2::widget([
        'model' => $model,
        'attribute' => $customer_id,
        'initValueText' => $name,
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
}