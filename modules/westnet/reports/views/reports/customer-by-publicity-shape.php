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

$this->title = Yii::t('app', 'Customers by publicity shape');
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
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="row"><div class="col-md-12">&nbsp;</div></div>

        <div class="row">
            <div class="col-md-12 text-center">
                <?= ChartJs::widget([
                    'type' => 'bar',
                    'options' => [
                        'width' => 800,
                        'height' => 400,
                    ],
                    'data' => [
                        'labels' => $cols,
                        'datasets' => [
                                [
                                        'label' => 'Medios y períodos',
                                        'data' => $data,
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