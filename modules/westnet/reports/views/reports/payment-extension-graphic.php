<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use dosamigos\chartjs\ChartJs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Payment extension graphic');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="customer-index">
        <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <div class="customer-search">
            <?php $form = ActiveForm::begin(['method' => 'POST']); ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $form->field($model, 'date_from')->widget(DatePicker::class, [
                            'language' => Yii::$app->language,
                            'model' => $model,
                            'attribute' => 'date_from',
                            'dateFormat' => 'dd-MM-yyyy',
                            'options'=>[
                                'class'=>'form-control filter dates',
                                'placeholder'=>Yii::t('app','Date')
                            ]
                        ])?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $form->field($model, 'date_to')->widget(DatePicker::class, [
                            'language' => Yii::$app->language,
                            'model' => $model,
                            'attribute' => 'date_to',
                            'dateFormat' => 'dd-MM-yyyy',
                            'options'=>[
                                'class'=>'form-control filter dates',
                                'placeholder'=>Yii::t('app','Date')
                            ]
                        ])?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="row"><div class="col-md-12">&nbsp;</div></div>

        <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget(
                    [
                        'type' => 'line',
                        'data' => [
                            'labels' => $colslineal,
                            'datasets' => [
                                [
                                    'label' => "Extensiones de pago desde APP",
                                    'fill' => false,
                                    'lineTension' => 0.1,
                                    'backgroundColor' => "rgba(255, 255, 255, 0)",
                                    'borderColor' => "rgba(241, 134, 132)",
                                    'borderCapStyle' => 'round',
                                    'borderDash' => [],
                                    'data' => $data_app,
                                    'fill' => true
                                ],
                                [
                                    'label' => "Extensiones de pago desde IVR",
                                    'fill' => false,
                                    'lineTension' => 0.1,
                                    'backgroundColor' => "rgba(255, 255, 255, 0)",
                                    'borderColor' => "rgba(241, 183, 132)",
                                    'borderCapStyle' => 'round',
                                    'borderDash' => [],
                                    'data' => $data_ivr,
                                    'fill' => true
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
        <div class="row" style="padding-top: 100px">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
                    'type' => 'polarArea',
                    'options' => [
                        'width' => 800,
                        'height' => 400,
                    ],
                    'data' => [
                        'labels' => $cols_tart,
                        'datasets' => [
                            [
                                'label' => 'Clientes',
                                'data' => $data_tart,
                                'backgroundColor' => $colors,
                                'borderColor' => $border_colors,
                                'borderWidth' => 1
                            ]]
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
    </div>