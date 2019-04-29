<?php

use app\modules\zone\models\Zone;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$zone_id = $model->zone_id;

$zone = Zone::findOne(['zone_id'=> $zone_id]);
$name = empty($zone) ? '' : $zone->getFullZone($zone->zone_id);

if (!isset($clear)) {
    $clear = true;
}

//Plugin options
$pluginOptions = [
    'allowClear' => $clear,
    'minimumInputLength' => 3,
    'ajax' => [
        'url' => Url::to(['/zone/zone/zones-by-name']),
        'dataType' => 'json',
        'data' => new JsExpression('function(params) { return {name:params.term}; }')
    ],
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    'templateResult' => new JsExpression('function(zone) { return zone.text; }'),
    'templateSelection' => new JsExpression('function (zone) { return zone.text; }'),
    'cache' => true
];

//Con ActiveForm
if(isset($form)){
    echo $form->field($model, 'zone_id')->label(isset($label) ? $label : null)->widget(Select2::classname(), [
        'data' => [$zone_id => $name],
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
    
//Sin ActiveForm
}else{
    echo Select2::widget([
        'model' => $model,
        'attribute' => $zone_id,
        'data' => [$zone_id => $name],
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
}

?>
