
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\web\View;
use \yii\grid\GridView;

$filename='plans_per_month_'.date('d_m_Y');
header('Content-type:application/xls');
header('Content-Disposition: attachment; filename='.$filename.'.xls');
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
        ])
?>