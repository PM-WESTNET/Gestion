<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use app\components\companies\CompanySelector;
use yiier\chartjs\ChartJs;
use yii\helpers\Url;

$this->title = '¿Cómo conoció la Empresa?';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-intention-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <hr>
    </div>

    <div class="customer-search">
        <?php $form = ActiveForm::begin(['method' => 'POST']); ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <?= CompanySelector::widget([
                        'model' => $reportSearch,
                        'attribute' => 'company_id',
                        'inputOptions' => [
                            'prompt' => Yii::t('app', 'All')
                        ]
                    ]) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($reportSearch, 'date_from'); ?>
                    <?= DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $reportSearch,
                        'attribute' => 'date_from',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options' => [
                            'class' => 'form-control filter dates',
                            'placeholder' => Yii::t('app', 'Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($reportSearch, 'date_to'); ?>
                    <?= DatePicker::widget([
                        'language' => Yii::$app->language,
                        'model' => $reportSearch,
                        'attribute' => 'date_to',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options' => [
                            'class' => 'form-control filter dates',
                            'placeholder' => Yii::t('app', 'Date')
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success', 'name' => 'search']) ?>
            </div>
            <div class="col-md-1">
                <a href=<?= Url::to(
                            [
                                '/reports/reports-company/how-did-you-know-the-company'
                            ],
                            true
                        )
                        ?> class="btn btn-success">
                    Limpiar
                </a>
            </div>
            <div class="col-md-1">
                <?php
                //var_dump($reportSearch);die(); 
                $date_from = (!empty($reportSearch->date_from)) ? $reportSearch->date_from : "";
                $date_to = (!empty($reportSearch->date_to)) ? $reportSearch->date_to : "";
                $company_id = (!empty($reportSearch->company_id)) ? $reportSearch->company_id : "";

                ?>
                <?= Html::a(
                    'exportar a excel',
                    [
                        '/reports/reports-company/how-did-you-know-the-company-excel',
                        'date_from' => $date_from, 'date_to' => $date_to, 'company_id' => $company_id,
                    ],
                    ['class' => 'btn btn-info', 'target' => '_blank']
                )
                ?>

            </div>
        </div>
        <?php ActiveForm::end();  ?>
    </div>


    <?= GridView::widget([

        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'publicity_shape',
                'format' => 'raw',
                'label' => Yii::t('app', 'Publicity Shape'),
                'value' => function ($model) {
                    return strtoupper(Yii::t('app', $model->publicity_shape));
                },

            ],
            [
                'attribute' => 'total_client',
                'format' => 'raw',
                'label' => Yii::t('app', 'Total'),
                'value' => function ($model) {
                    return $model->total_client;
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $from_date = null;
                        $to_date = null;
                        $company = null;
                        if (isset(Yii::$app->request->post()['ReportSearch'])) {
                            $data = Yii::$app->request->post()['ReportSearch'];
                            $from_date = $data['date_from'];
                            $to_date = $data['date_to'];
                            $company = $data['company_id'];
                        }
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['reports-company/how-did-you-know-the-company-view-customer', 'publicity_shape' => $model->publicity_shape, 'from_date' => $from_date, 'to_date' => $to_date, 'company' => $company], ['data-pjax' => '0']);
                    }
                ]
            ]
        ],

    ]);

    ?>

    <?php
    $colorsArr = [];
    foreach ($label as $item) {
        foreach (array('r', 'g', 'b') as $color) {
            //Generate a random number between 0 and 255.
            $rgbColor[$color] = mt_rand(0, 255);
        }
        $colorsArr[] = "rgb(" . implode(",", $rgbColor) . ")";
    }
    ?>
    <br>
    <br>
    <center>
        <h1>GRAFICAS DE DATOS</h1>
    </center>
    <br>

    <?php echo ChartJs::widget([
        'type' => 'bar',
        'data' => [
            'labels' => $label,
            'datasets' => [
                [
                    'label' => "REPORTE DE BARRAS",
                    'backgroundColor' => 'rgba(28, 54, 162, 0.5)',
                    'borderColor' => 'rgba(28, 54, 162, 1)',
                    'borderWidth' => 2,
                    'data' => $data,
                ],
            ]
        ]
    ]); ?>
    <br>
    <br>
    <?= ChartJs::widget([
        'type' => 'polarArea',
        'data' => [
            'labels' => $label,
            'datasets' => [
                [
                    'label' => "POLAR REPORT",
                    'backgroundColor' => 'rgba(28, 54, 162, 0.5)',
                    'borderColor' => 'rgba(28, 54, 162, 1)',
                    'borderWidth' => 2,
                    'data' => $data,
                ],
            ]
        ],


    ]); ?>
</div>