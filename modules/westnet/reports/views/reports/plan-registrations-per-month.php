<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;

$this->title = 'Altas de Planes por Mes';

?>

<div class="customer-registrations">

    <h1><?php echo $this->title ?></h1>
    <form action="customer-registrations" method="GET">
        <?php echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $reportSearch,
            'columns' => [
                [
                    'attribute' => 'groupDate',
                    'format' => 'text',
                    'label' => 'Año y Mes (Agrupado x plan)',
                ],
                [
                    'attribute' => 'pName',
                    'format' => 'text',
                    'label' => 'Nombre del plan completo',
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
    </form>


</div>