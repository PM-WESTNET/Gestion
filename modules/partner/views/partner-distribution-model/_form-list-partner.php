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
            'label' => Yii::t("partner", "Partner"),
            'attribute' => 'partner.name'
        ],
        [
            'label' => Yii::t("app", "Percentage"),
            'attribute' => 'percentage'
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{update} {delete}',
            'buttons'=>[
                'delete'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['partner-distribution-model/delete-partner', 'partner_distribution_model_has_partner_id'=>$model->partner_distribution_model_has_partner_id]),
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'deleteItem btn btn-danger'
                        ]);
                },
                'update'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['partner-distribution-model/add-partner', 'partner_distribution_model_id'=>$model->partner_distribution_model_id, 'partner_distribution_model_has_partner_id'=>$model->partner_distribution_model_has_partner_id]),
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