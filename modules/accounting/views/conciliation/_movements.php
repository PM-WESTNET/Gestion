<?php
use yii\grid\GridView;

$cols = [];
if (!$readOnly) {
    $cols[] = [
        'class' => 'yii\grid\CheckboxColumn',
        'checkboxOptions' => function($model, $key, $index, $column) {
            return ['value' => $model['account_movement_item_id']];
        }
    ];
}
$cols = array_merge($cols, [
    [
        'header'=>Yii::t('app', 'Date'),
        'value' => function ($model) {
            return ($model['date'] ? Yii::$app->formatter->asDate($model['date'], 'dd-MM-yyyy')  : '' );
        }
    ],
    [
        'label' => 'Cuit',
        'value' => function ($model) {
            $customer = \app\modules\accounting\models\AccountMovement::searchCustomer($model['account_movement_id']);
            if($customer) {
                    return $customer->document_number;
            }

            return null;
        }
    ],
    [
        'label' => 'Cuit2',
        'value' => function ($model) {
            $customer = \app\modules\accounting\models\AccountMovement::searchCustomer($model['account_movement_id']);
            if($customer) {
                return $customer->profile_cuit2;
            }

            return null;
        }
    ],
    [
        'header'=>Yii::t('app', 'Description'),
        'attribute'=>'description',
    ],
    [
        'header'=>Yii::t('app', 'Debit'),
        'value' => function ($model) {
            return Yii::$app->formatter->asCurrency($model['debit']);
        }
    ],
    [
        'header'=>Yii::t('app', 'Credit'),
        'value' => function ($model) {
            return Yii::$app->formatter->asCurrency($model['credit']);
        }
    ],

]);

echo GridView::widget([
    //'layout'=> '{items}{}',
    'id'=>'wDebit',
    //'caption' => Yii::t('accounting', 'Debits with out conciliation.'),
    'dataProvider' => $movementsDataProvider,
    'columns' => $cols,
    'summary' => ''
]);?>