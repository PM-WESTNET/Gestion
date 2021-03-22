<?php


use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$customerCodeAds = $model->customerCodeADS;

$ads = app\modules\westnet\models\EmptyAds::findOne(['code'=> $customerCodeAds]);




//Plugin options
$pluginOptions = [
    'allowClear' => true,
    'minimumInputLength' => 1,
    'ajax' => [
        'url' => Url::to(['/westnet/empty-ads/search-ads']),
        'dataType' => 'json',
        'data' => new JsExpression('function(params) { return {code:params.term}; }')
    ],
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    'templateResult' => new JsExpression('function(ads) { return ads.text; }'),
    'templateSelection' => new JsExpression('function (ads) { return ads.text; }'),
    'cache' => true
];

//Con ActiveForm
if(isset($form)){
    echo $form->field($model, 'customerCodeADS')->label(Yii::t('app', 'Code'))->widget(Select2::classname(), [
        'data' => [$customerCodeAds => $customerCodeAds],
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
    
//Sin ActiveForm
}else{
    echo Select2::widget([
        'model' => $model,
        'attribute' => 'customerCodeADS',
        'data' => [$customerCodeAds => $customerCodeAds],
        'options' => ['placeholder' => Yii::t('app', 'Search')],
        'pluginOptions' => $pluginOptions,
    ]);
}

?>
