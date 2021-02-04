<?php
use app\modules\provider\models\Provider;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$provider_id = ($attribute ? $attribute : 'provider_id' );

$customer = Provider::findOne($model->$provider_id);
$name = empty($model->$provider_id) ? '' : $customer->name;

echo $form->field($model, $provider_id)->label(isset($label) ? $label : null)->widget(Select2::classname(), [
    'initValueText' => $name,
    'options' => ['placeholder' => Yii::t('app', 'Search')],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'ajax' => [
            'url' => Url::to(['/provider/provider/find-by-name']),
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {name:params.term, id:$(this).val()}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('function(provider) { return provider.text; }'),
        'templateSelection' => new JsExpression('function (provider) { return provider.text; }'),
        'cache' => true
    ],
]);