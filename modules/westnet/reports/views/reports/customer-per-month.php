<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use dosamigos\chartjs\ChartJs;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Active Customers per month');
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
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success pull-right']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="row"><div class="col-md-12">&nbsp;</div></div>

        <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
                    'type' => 'line',
                    'options' => [
                        'width' => 800,
                        'height' => 400,
                    ],
                    'data' => [
                        'labels' => $cols,
                        'datasets' => [
                            [
                                'label' => \Yii::t('app', 'Customers'),
                                'backgroundColor' => "rgba(38, 14, 154, 0.2)",
                                'pointRadius' => 6,
                                'pointHitRadius' => 6,
                                'pointHoverRadius' => 4,
                                'pointBackgroundColor' => "rgba(38, 14, 154, 1)",
                                'borderColor' => "rgba(38, 14, 154, 1)",
                                'pointStrokeColor' => "rgba(38, 14, 154, 1)",
                                'tension' => 0,
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
                Se tienen en cuenta los contratos activos que la fecha de finalizaci??n sea menor al dia de la consulta (ultimo dia del mes) o sea nula.
            </p>
            <img >
        </div>
    </div>