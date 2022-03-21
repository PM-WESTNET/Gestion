<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use dosamigos\chartjs\ChartJs;

$this->title = 'Estadística de Uso de la APP';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="statistic-app-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <hr>
    </div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'type',
            'description',
            'total'
        ],

    ]); ?>
    <hr>
    <?php
        $statisticAppRegs = $dataProvider->getModels();
        if(!empty($statisticAppRegs)):
            foreach ($statisticAppRegs as $key => $value) {
                $rows[] = $value->total;
                $cols[] = $value->type;
            }
    ?>

    <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget(
                    [
                        'type' => 'bar',
                        'data' => [
                            'labels' => $cols,
                            'datasets' => [[
                                'label' => 'Uso de la Aplicación',
                                'data' => $rows,
                                'backgroundColor' => [
                                  'rgba(255, 99, 132, 0.2)',
                                  'rgba(255, 159, 64, 0.2)',
                                  'rgba(255, 205, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)',
                                  'rgba(54, 162, 235, 0.2)',
                                  'rgba(153, 102, 255, 0.2)',
                                  'rgba(201, 203, 207, 0.2)'
                                ],
                                'borderColor' => [
                                  'rgb(255, 99, 132)',
                                  'rgb(255, 159, 64)',
                                  'rgb(255, 205, 86)',
                                  'rgb(75, 192, 192)',
                                  'rgb(54, 162, 235)',
                                  'rgb(153, 102, 255)',
                                  'rgb(201, 203, 207)'
                                ],
                                'borderWidth' => 1
                              ]
                            ],
                        ],
                        'clientOptions' => [
                            'scales' => [
                                'yAxes' => [
                                    'ticks' => [
                                        'min' => 0
                                    ]
                                ],
                                'xAxes' => [
                                    'ticks' => ['min' => 0]
                                ]
                            ]
                        ]
                    ]
                ); ?>
            </div>
        </div>
    <hr>
    <div class="row" style="padding-top: 100px">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
                    'type' => 'polarArea',
                    'options' => [
                        'width' => 800,
                        'height' => 400,
                    ],
                    'data' => [
                        'labels' => $cols,
                        'datasets' => [
                            [
                                'label' => 'Clientes',
                                'data' => $rows,
                                'backgroundColor' => [
                                  'rgba(255, 99, 132, 0.2)',
                                  'rgba(255, 159, 64, 0.2)',
                                  'rgba(255, 205, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)',
                                  'rgba(54, 162, 235, 0.2)',
                                  'rgba(153, 102, 255, 0.2)',
                                  'rgba(201, 203, 207, 0.2)'
                                ],
                                'borderColor' => [
                                  'rgb(255, 99, 132)',
                                  'rgb(255, 159, 64)',
                                  'rgb(255, 205, 86)',
                                  'rgb(75, 192, 192)',
                                  'rgb(54, 162, 235)',
                                  'rgb(153, 102, 255)',
                                  'rgb(201, 203, 207)'
                                ],
                                'borderWidth' => 2
                            ]
                        ]
                    ],
                    'options' => [
                        'scales' => [
                            'yAxes' => [
                                [
                                    'ticks' => [
                                        'beginAtZero' => true
                                    ]
                                ]
                            ]
                        ]
                    ]

                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
