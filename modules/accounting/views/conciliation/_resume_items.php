<?php

use app\modules\accounting\models\ConciliationItem;
use app\modules\accounting\models\Resume;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\Dialog;
use yii\widgets\Pjax;

?>


<?php
/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */
if ($resumeItemsDataProvider) {
    $cols = [];
    if (!$readOnly){
        $cols[] = [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function($model, $key, $index, $column) {
                return ['value' => $model->resume_item_id];
            }
        ];
    }

    $cols = array_merge($cols, [
        [
            'header'=>Yii::t('app', 'Date'),
            'attribute'=>'date',
            'format'=>['date']
        ],
        [
            'header'=>Yii::t('app', 'Operation Type'),
            'value' => function($model) {
                $operation = $model->operationType;

                if($operation) {
                    return $operation->name;
                }
            },
        ],
        [
            'header'=>Yii::t('app', 'Description'),
            'attribute'=>'description',
        ],

        [
            'header'=>Yii::t('accounting', 'Debit'),
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model->debit);
            }
        ],
        [
            'header'=>Yii::t('accounting', 'Credit'),
            'value' => function ($model) {
                return Yii::$app->formatter->asCurrency($model->credit);
            }
        ],

    ]);

    echo GridView::widget([
        //'layout'=> '{items}',
        'id'=> 'w_resume_items_debit',
        'dataProvider' => $resumeItemsDataProvider,
        /**'rowOptions' => function ($model) {
            if ($model->ready) {
                return [
                    'style' => 'background-color: #7FFFB2'
                ];
            }

            return [];
        },**/
        'summary' => '',
        'columns' => $cols
    ]);

}

