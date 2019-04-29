<?php
use app\modules\sale\models\Customer;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\Html;

$customer_id = ($model_attribute ? $model_attribute : 'customer_id' );

$customers = Customer::find()->where([$table_attribute => $model->$customer_id])->all();
$names= [];

if (!empty($customers)) {
    foreach ($customers as $c){
        $names[] = $c->fullName;
    }
}


//Plugin options
$pluginOptions = [
    'multiple' => true,
    'allowClear' => true,
    'minimumInputLength' => 3,
    'ajax' => [
        'url' => Url::to(['/sale/customer/find-by-name']),
        'dataType' => 'json',
        'data' => new JsExpression('function(params) { return {name:params.term, id: null, normal: 0}; }')
    ],
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    'templateResult' => new JsExpression('function(customer) { return customer.text; }'),
    'templateSelection' => new JsExpression('function (customer) { return customer.text; }'),
    'cache' => true
];

//Con ActiveForm
if(isset($form)){
    echo $form->field($model, $customer_id)->label(isset($label) ? $label : null)->widget(Select2::classname(), [
        'value' => $names,
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
    
//Sin ActiveForm
}else{
    echo Select2::widget([
        'model' => $model,
        'attribute' => $customer_id,
        'initValueText' => $names,
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
}