<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountMovement */
/* @var $form yii\widgets\ActiveForm */
    // Listado de Items
echo GridView::widget([
    'id'=>'items',
    'dataProvider' => $items,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => Yii::t("accounting", "Operation Type"),
            'value' => function($model){
                return ($model->operationType ? $model->operationType->name : '' );
            },
        ],
        [
            'label' => Yii::t("accounting", "Account"),
            'attribute' => 'account.name'
        ],
        [
            'label' => Yii::t("accounting", "Money Box Account"),
            'attribute' => 'moneyBoxAccount.number'
        ],
        'operationType.is_debit:boolean',
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{update} {delete}',
            'buttons'=>[
                'delete'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['money-box/delete-operation-type', 'money_box_id'=>$model->money_box_id, 'money_box_has_operation_type_id'=>$model->money_box_has_operation_type_id]),
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'deleteItem btn btn-danger'
                        ]);
                },
                'update'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['money-box/add-operation-type', 'money_box_id'=>$model->money_box_id, 'money_box_has_operation_type_id'=>$model->money_box_has_operation_type_id]),
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'updateItem btn btn-primary'
                        ]);
                }
            ]
        ],
    ],
    'options'=>[
        'style'=>'margin-top:10px;'
    ]
]);
?>