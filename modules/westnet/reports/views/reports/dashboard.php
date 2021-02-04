<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yiier\chartjs\ChartJs;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">
    <div class="title text-center">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="customer-search">
        <?php $form = ActiveForm::begin(['method' => 'POST']); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($model, 'date_from'); ?>
                    <?php
                    echo yii\jui\DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $model,
                        'attribute' => 'date_from',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($model, 'date_to'); ?>
                    <?php
                    echo yii\jui\DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $model,
                        'attribute' => 'date_to',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ]);
                    ?>
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
    <div class="row"><div class="col-md-12"></div></div>

    <div class="row">
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Total Customer Variation')?></strong> </div>
            <?php

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'bar',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $labels_udv,
                    'datasets' => $datasets_udv
                ]
            ]);
            ?>
        </div>
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Company Passive')?></strong> </div>
            <?php

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $cols_company_passive,
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Percentage'),
                            'backgroundColor' => "rgba(255, 99, 132, 0.2)",
                            'borderColor' => "rgba(255, 99, 132, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'data' => $data_company_passive
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Cost effectiveness')?></strong> </div>
            <?php
            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'bar',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                    'legend' => [
                        'position' => 'top',
                        'display' => 'true'
                    ]
                ],
                'data' => [
                    'labels' => $labels_cf,
                    'datasets' => $datasets_cf
                ]
            ]);
            ?>
        </div>
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Customers Variation per month')?></strong> </div>
            <?php
            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $cols_cvpm,
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Connections'),
                            'backgroundColor' => "rgba(255, 99, 132, 0.2)",
                            'borderColor' => "rgba(50, 99, 132, 0.2)",
                            'pointBorderColor' => $colors_cvpm,
                            'pointBackgroundColor' => $colors_cvpm,
                            'data' => $data_cvpm,
                            'fill'=> false,
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="row">

        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Active Customers per month')?></strong> </div>
            <?php
            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $cols_cp,
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Customers'),
                            'backgroundColor' => "rgba(255, 99, 132, 0.2)",
                            'borderColor' => "rgba(255, 99, 132, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'data' => $datas_cp
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
-
    </div>
    <div class="row">
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Low By Month')?></strong> </div>
            <?php

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $cols_lbm,
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Percentage'),
                            'backgroundColor' => "rgba(255, 99, 132, 0.2)",
                            'borderColor' => "rgba(255, 99, 132, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'data' => $data_lbm,
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?=ReportsModule::t('app', 'Low By Reason')?></strong> </div>
            <?php
            $from = new \DateTime($model->date_from);
            $to = new \DateTime($model->date_to);

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'clientOptions' => [
                    'width' => 800,
                    'height' => 400,
                    'scales' => [
                        'xAxes' => [[
                            'stacked' => true
                        ]],
                        'yAxes' => [[
                            'stacked' => true
                        ]],
                    ]
                ],
                'data' => [
                    'labels' => $labels_lbr,
                    'datasets' => $dataset_lbr
                ]
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'1'])?></strong></div>
            <?php
            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $data1['cols'],
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Percentage'),
                            'backgroundColor' => "rgba(0, 225, 0, 0.2)",
                            'borderColor' => "rgba(0, 225, 0, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'fill' => false,
                            'data' => $data1['data'],
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
        <div class="col-md-6 text-center">
            <div class=" text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'2'])?></strong></div>
            <?php

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $data2['cols'],
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Percentage'),
                            'backgroundColor' => "rgba(0, 255, 255, 0.2)",
                            'borderColor' => "rgba(0, 255, 255, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'fill' => false,
                            'data' => $data2['data'],
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'3'])?></strong></div>
            <?php

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $data3['cols'],
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Percentage'),
                            'backgroundColor' => "rgba(255, 0, 0, 0.2)",
                            'borderColor' => "rgba(255, 0, 0, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'fill' => false,
                            'data' => $data3['data'],
                        ],
                    ]
                ]
            ]);
            ?>
        </div>

        <div class="col-md-6 text-center">
            <div class="text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'4'])?></strong></div>
            <?php

            echo \dosamigos\chartjs\ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    'labels' => $data4['cols'],
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Percentage'),
                            'backgroundColor' => "rgba(100, 100, 100, 0.2)",
                            'borderColor' => "rgba(100, 100, 100, 0.2)",
                            'pointStrokeColor' => "#fff",
                            'fill' => false,
                            'data' => $data4['data'],
                        ],
                    ]
                ]
            ]);
            ?>
        </div>

    </div>
</div>