<?php

use app\modules\config\models\Config;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = Yii::t('app', 'View History');
$this->params['breadcrumbs'][] = Yii::t('app', 'View History');
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
        /* [
            'attribute' => 'company_id',
            'value' => function ($model){
                return $model->company->name;
            }
        ] */
        [
            'label' => 'company name',
            'value' => 'company.name'
        ],
        [
            'label' => 'bill type',
            'value' => 'billType.name'
        ],
        'period',
        'status',
        'type',
        [
            'label' => 'Inicio',
            'value' => function ($model){
                return ($model->start_datetime)?date("Y-m-d H:i:s", $model->start_datetime):null;
            }
        ],
        [
            'label' => 'Fin',
            'value' => function ($model){
                return ($model->end_datetime)?date("Y-m-d H:i:s", $model->end_datetime):null;
            }
        ]
    ],
])

?>