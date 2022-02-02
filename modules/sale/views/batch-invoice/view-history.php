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
        ],
        [
            'label' => 'Tiempo',
            'value' => function ($model){
                if(empty($model->start_datetime) or empty($model->end_datetime)) return null;
                $datetime_start = date_create(date("Y-m-d H:i:s", $model->start_datetime));
                $datetime_end = date_create(date("Y-m-d H:i:s", $model->end_datetime));
                $interval = date_diff($datetime_start, $datetime_end);
                return $interval->format('%h Horas %i Minutos %s Segundos');              
            }
        ]
    ],
])

?>