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

$this->title = ReportsModule::t('app', 'Debt Evolution');
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
    <div class="row"><div class="col-md-12"></div></div>

    <div class="row">
        <div class="col-md-12 text-center">
            <?php
            $cols = [];
            $invoices = [];
            $dataset = [];
            $colors = [
                'rgba(255, 235, 238,1.0)',
                'rgba(156, 39, 176,1.0)',
                'rgba(63, 81, 181,1.0)',
                'rgba(33, 150, 243,1.0)',
                'rgba(0, 150, 136,1.0)',
                'rgba(205, 220, 57,1.0)',
                'rgba(255, 193, 7,1.0)',
                'rgba(255, 152, 0,1.0)',
                'rgba(255, 87, 34,1.0)',
                'rgba(158, 158, 158,1.0)',
                'rgba(121, 85, 72,1.0)'
            ];

            /** @var \app\modules\westnet\models\DebtEvolution $de */
            foreach($data as $de){
                $cols[] = $de->period;
                $invoices['x1'][]   = $de->invoice_1;
                $invoices['x2'][]   = $de->invoice_2;
                $invoices['x3'][]   = $de->invoice_3;
                $invoices['x4'][]   = $de->invoice_4;
                $invoices['x5'][]   = $de->invoice_5;
                $invoices['x6'][]   = $de->invoice_6;
                $invoices['x7'][]   = $de->invoice_7;
                $invoices['x8'][]   = $de->invoice_8;
                $invoices['x9'][]   = $de->invoice_9;
                $invoices['x10'][]  = $de->invoice_10;
                $invoices['x11'][]  = $de->invoice_x;
            }

            $i = 1;
            foreach($invoices as $invoice){
                $dataset[] = [
                        'label'             => ReportsModule::t('app', 'Debt' ) . ' ' .($i<10 ? $i : ReportsModule::t('app', 'more than 10') ) ,
                        'backgroundColor'   => $colors[$i-1],
                        'borderColor'       => $colors[$i-1],
                        'data'              => $invoices['x'.$i],
                        'fill'              =>  false
                    ];
                $i++;
            }

            for($i=0; $i<4; $i++){
                echo ChartJs::widget([
                    'type' => 'line',
                    'options' => [
                        'responsive' => true,
                        'width' => 800,
                        'height' => 200,
                    ],
                    'data' => [
                        'labels' => $cols,
                        'datasets' => [
                            $dataset[$i]
                        ]
                    ]
                ]);
                echo '<div class="row"><div class="col-md-12">&nbsp;</div></div>';
            }
            echo ChartJs::widget([
                'type' => 'line',
                'options' => [
                    'responsive' => true,
                    'width' => 800,
                    'height' => 200,
                ],
                'data' => [
                    'labels' => $cols,
                    'datasets' => array_splice($dataset, 4)
                ]
            ]);

            ?>
        </div>
    </div>
</div>