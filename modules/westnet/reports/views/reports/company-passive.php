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

$this->title = ReportsModule::t('app', 'Company Passive');
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
        <div class="row"><div class="col-md-12">&nbsp;</div></div>

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
                        'labels' => $cols,
                        'datasets' => [
                            [
                                'label' => \Yii::t('app', 'Percentage'),
                                'backgroundColor' => "rgba(255, 99, 132, 0.2)",
                                'borderColor' => "rgba(255, 99, 132, 0.2)",
                                'pointStrokeColor' => "#fff",
                                'data' => $data
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
                1 - Se calcula el saldo total deudor al ??ltimo dia del mes (misma consulta de
                deudores del sistema) <br>
                2 - Se calcula el total facturado del mes. <br>
                3 - Se calcula el % haciendo: (deuda/facturado)*100 <br>
            </p>
            <?= Html::label(Yii::t('app', 'Image References'))?>
            <div class="col-md-12">
                <div class="col-md-6">
                    <?= Html::img('@web/images/report-reference/Pasivo1.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
            </div>
        </div>
    </div>