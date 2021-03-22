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

$this->title = ReportsModule::t('app', 'Reasons of low');
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
                $values = [];
                $labels = [];
                $dataset = [];

                do {
                    $labels[] = $from->format('m/Y');
                    foreach ($categories as $category) {
                        $values[$category->name][$from->format('m/Y')] = 0;
                    }
                    $from->modify('first day of next month');
                }while($from <= $to);


                foreach ($data as $item) {
                    $values[$item['name']][$item['period']] = $item['cant'];
                }

                foreach ($values as $key=>$item) {
                    $dataset[] = [
                        'label' => $key,
                        'data'=> array_values($item),
                        'backgroundColor' => sprintf('rgba(%s,%s,%s,1)', rand(1,255), rand(1,255), rand(1,255)),
                    ];
                }


                echo ChartJs::widget([
                    'type' => 'bar',
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
    </div>