<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
    // Listado de Items
echo GridView::widget([
    'id'=>'items',
    'dataProvider' => $items,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => Yii::t("app", "Parent Company"),
            'attribute' => 'company.name'
        ],
        [
            'label' => Yii::t('westnet', 'Billing'),
            'value' => function($model){
                $html = "";
                $html .=
                    '1er ' . ($model->firstCompany ? $model->firstCompany->name : Yii::t('westnet', 'Not defined') ).'<br/>' .
                    '2da ' . ($model->secondCompany ? $model->secondCompany->name : Yii::t('westnet', 'Not defined') ).'<br/>' .
                    '3er ' . ($model->thirdCompany ? $model->thirdCompany->name : Yii::t('westnet', 'Not defined') );
                ;
                return $html;
            },
            'format' => 'html'
        ],
        [
            'class' => 'app\components\grid\ActionColumn',
            'template'=>'{delete}',
            'buttons'=>[
                'delete'=>function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        null,
                        [
                            'data-url' => yii\helpers\Url::toRoute(['/westnet/node/delete-companies', 'node_has_companies_id'=>$model->node_has_companies_id]),
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'deleteItem btn btn-danger',
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