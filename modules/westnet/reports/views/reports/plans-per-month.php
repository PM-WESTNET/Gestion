<?php
// $data = yii\helpers\Json::htmlEncode($dataProvider->getModels());
// var_dump($data);
// die();

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use yii\web\View;
use yii\widgets\Pjax;
use \yii\grid\GridView;

$this->title = 'Altas de Planes por Mes';

?>

<div class="customer-registrations">

    <h1><?php echo $this->title ?></h1>
    <form action="customer-registrations" method="GET">
        <?= Html::a(
                'exportar a excel',
                [
                    '/reports/reports/plans-per-month','excel-export'=>true
                ],
                [
                    'class' => 'btn btn-info',
                    'target' => '_blank'
                ]
            )
        ?>
        <?php 
            Pjax::begin(); 
        ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $reportSearch,
            'columns' => [
                [
                    'attribute' => 'groupDate',
                    'label' => 'Año y Mes (Agrupado x plan)',
                    'filter'=>DateRangePicker::widget([
                        'model' => $reportSearch,
                        'attribute' => 'groupDate',
                        'presetDropdown' => true
                    ]),
                ],
                [
                    'attribute' => 'pName',
                    'format' => 'text',
                    'label' => 'Nombre del plan completo',
                    'filter' => $plansArray, // mapped from ReportsController
                ],
                [
                    'attribute' => 'cantAltasPorMes',
                    'format' => 'html',
                    'label' => 'Altas de Plan por Mes',
                    'value' => function ($model) {
                        return Html::a(
                            $model['cantAltasPorMes'],
                            yii\helpers\Url::toRoute([
                                '/reports/reports/customers-per-plan-per-month', 
                                'product_id' => $model['product_id'], 
                                'year_month' => $model['groupDate']
                            ])
                        );
                        
                    },
                ],
            ]
        ]) ?>
        <?php 
            Pjax::end(); 
        ?>
    </form>


</div>

