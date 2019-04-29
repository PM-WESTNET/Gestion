<?php
/** 
 * Al ser utilizado en un grid, se optimiza enviando los objetos inflector y el array de billTypes.
 */
if(!isset($inflector)){
    $inflector = \app\components\helpers\Inflector::getInflector();
}
if(!isset($billTypes)){
    $billTypes = app\modules\sale\models\BillType::find()->orderBy(['class' => SORT_ASC, 'name' => SORT_ASC])->all();
}

$billItems = [];

//Utilizamos lastClass para ubicar divisores por clase de comprobante
$lastClass = null;

foreach($billTypes as $item){
    //Solo mostramos tipos que el cliente puede tener asociados
    if($model->checkBillType($item)){
        if($lastClass != null && $item->class != $lastClass){
            $billItems[] = '<li class="divider"></li>';
        }
        
        $lastClass = $item->class;
        $billItems[] = ['label' => $inflector->pluralize($item->name). " ({$model->countBills($item->bill_type_id)})", 'url' => [
            '/sale/bill/index', 
            'BillSearch[bill_types][]' => $item->bill_type_id,
            'BillSearch[customer_id]' => $model->customer_id
        ],'items' => [
            ['label' => Yii::t('app', 'Draft'). " ({$model->countBills($item->bill_type_id, 'draft')})", 'url' => [
                '/sale/bill/index',
                'BillSearch[bill_types][]' => $item->bill_type_id,
                'BillSearch[customer_id]' => $model->customer_id,
                'BillSearch[statuses][]' => 'draft',
            ], 'visible' => $model->countBills($item->bill_type_id, 'draft')],
            ['label' => Yii::t('app', 'Completed'). " ({$model->countBills($item->bill_type_id, 'completed')})", 'url' => [
                '/sale/bill/index',
                'BillSearch[bill_types][]' => $item->bill_type_id,
                'BillSearch[customer_id]' => $model->customer_id,
                'BillSearch[statuses][]' => 'completed'
            ], 'visible' => $model->countBills($item->bill_type_id, 'completed')],
            ['label' => Yii::t('app', 'Closed'). " ({$model->countBills($item->bill_type_id, 'closed')})", 'url' => [
                '/sale/bill/index',
                'BillSearch[bill_types][]' => $item->bill_type_id,
                'BillSearch[customer_id]' => $model->customer_id,
                'BillSearch[statuses][]' => 'closed'
            ], 'visible' => $model->countBills($item->bill_type_id, 'closed')]
        ]];
            
    }

}

//Generamos un enlace para ver los comprobantes pendientes del cliente:
//https://docs.google.com/document/d/1rf44UE0cUj0rSmS5jy_U40R0VNuouh3RcQR7A3TWrtI/edit#heading=h.8653qyhayrkz
$nonEndpointTypes = \app\modules\sale\components\BillExpert::getNonEndpointBillTypes();
$url = ['/sale/bill/index', 'BillSearch[expired]' => 0, 'BillSearch[customer_id]' => $model->customer_id, 'BillSearch[statuses][]' => 'closed'];

foreach($nonEndpointTypes as $type){
    $url['BillSearch[bill_types][]'] = $type->bill_type_id;
}

$billItems[] = '<li class="divider"></li>';
$billItems[] = ['label' => Yii::t('app', 'Pending Bills'), 'url' => $url];

//Widget dropdown
echo yii\bootstrap\ButtonDropdown::widget([
    'label' => '<span class="glyphicon glyphicon-list-alt"></span> '. Yii::t('app','Bills'),
    'dropdown' => [
        'items' => $billItems,
        'encodeLabels'=>false,
        'options' => ['class' => 'dropdown-menu dropdown-menu-right']
    ],
    'encodeLabel' => false,
    'options'=>[
        'class'=>isset($class) ? $class : 'btn btn-warning',
    ]
]);