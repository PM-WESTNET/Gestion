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

$this->title = ReportsModule::t('app', 'Cost effectiveness');
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
            $cols = [];
            $facturado = [];
            $cobros = [];
            $diff = [];
            foreach($data as $item){
                $cols[] = $item['date'];
                $facturado[] = $item['facturado'];
                $pagos[] = $item['pagos'];
                $diff[] = $item['diferencia'];
            }
            echo ChartJs::widget([
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
                    'labels' => $cols,
                    'datasets' => [
                        [
                            'label' => ReportsModule::t('app', 'Billed'),
                            'backgroundColor' => "#337ab7",
                            'strokeColor' => "#337ab7",
                            'pointColor' => "rgba(220,220,220,1)",
                            'pointStrokeColor' => "#fff",
                            'data' => $facturado
                        ],
                        [
                            'label' => ReportsModule::t('app', 'Payed'),
                            'backgroundColor' => "#5cb85c",
                            'strokeColor' => "#5cb85c",
                            'pointColor' => "rgba(220,220,220,1)",
                            'pointStrokeColor' => "#fff",
                            'data' => $pagos
                        ],                        [
                            'label' => ReportsModule::t('app', 'Diff'),
                            'backgroundColor' => "#d9534f" ,
                            'strokeColor' => "#d9534f",
                            'pointColor' => "rgba(220,220,220,1)",
                            'pointStrokeColor' => "#fff",
                            'data' => $diff
                        ],
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
</div>