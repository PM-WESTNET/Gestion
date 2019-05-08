<?php

use app\components\companies\CompanySelector;
use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yiier\chartjs\ChartJs;
use yii\jui\DatePicker;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Debt Bills');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="customer-index">
        <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <div class="customer-search">
            <?php $form = ActiveForm::begin(['method' => 'POST']); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <?= CompanySelector::widget([
                            'model' => $model,
                            'attribute' => 'company_id',
                            'inputOptions' => [
                                'prompt' => Yii::t('app', 'All')
                            ]
                        ])?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= Html::activeLabel($model, 'date_from'); ?>
                        <?= DatePicker::widget([
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
                        <?= DatePicker::widget([
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
        <div class="row"><div class="col-md-12 text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'1'])?></strong></div></div>

        <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
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
        </div>

        <div class="row"><div class="col-md-12 text-center">&nbsp;</div></div>
        <div class="row"><div class="col-md-12 text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'2'])?></strong></div></div>

        <div class="row">
            <div class="col-md-12 text-center">
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

        <div class="row"><div class="col-md-12 text-center">&nbsp;</div></div>
        <div class="row"><div class="col-md-12 text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'3'])?></strong></div></div>

        <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
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
        </div>

        <div class="row"><div class="col-md-12 text-center">&nbsp;</div></div>
        <div class="row"><div class="col-md-12 text-center"><strong><?php echo ReportsModule::t('app', 'Customer With {i,plural,=1{One Debt Bill} other{Debts Bills}}', ['i'=>'4'])?></strong></div></div>

        <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
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
        <div style="padding-top: 20px">
            <?= Html::label(Yii::t('app', 'References'))?>
            <p>
                Se filtra la Consulta de deudores, por cantidad de facturas adeudadas.
            </p>
            <?= Html::label(Yii::t('app', 'Image References'))?>
            <div class="col-md-12">
                <div class="col-md-4">
                    <?= Html::img('@web/images/report-reference/Deben1.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
                <div class="col-md-4">
                    <?= Html::img('@web/images/report-reference/Deben2.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
                <div class="col-md-4">
                    <?= Html::img('@web/images/report-reference/Deben3.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
            </div>
        </div>
    </div>