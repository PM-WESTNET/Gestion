<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use dosamigos\chartjs\ChartJs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Low By Reason');
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
                        'labels' => $labels,
                        'datasets' => $dataset
                    ]
                ]);
                ?>
            </div>
        </div>
        <div style="padding-top: 20px">
            <?= Html::label(Yii::t('app', 'References'))?>
            <p>
                Se filtran todos los contratos dados de baja agrupados por razón, esta
                última es sacada de las categorías de baja de Mesa.
            </p>
            <?= Html::label(Yii::t('app', 'Image References'))?>
            <div class="col-md-12">
                <div class="col-md-6" style="padding-top: 20px">
                    <?= Html::img('@web/images/report-reference/BajasCausas.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
                <div class="col-md-6" style="padding-top: 20px">
                    <?= Html::img('@web/images/report-reference/BajasCausas2.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-6" style="padding-top: 20px">
                    <?= Html::img('@web/images/report-reference/BajasCausas3.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
                <div class="col-md-6" style="padding-top: 20px">
                    <?= Html::img('@web/images/report-reference/BajasCausas4.jpg', ['class' => 'img-responsive img-rounded align-center'])?>
                </div>
            </div>
        </div>
    </div>