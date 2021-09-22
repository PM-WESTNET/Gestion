<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;

$this->title = 'Valoración de Intenciones de Pago';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="valoration-payment-intention-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <hr>
    </div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'email',
            'description',
            [
                'attribute' => 'created_at',
                'label' => 'Fecha',
                'filter' => DateRangePicker::widget([
                    'name' => 'createTimeRange',
                    'model' => $searchModel,
                    'attribute' => 'from_date',
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'locale' => [
                            'format' => 'Y-m-d'
                        ],
                    ]
                ]),
            ], 
        ],

    ]); ?>

</div>
