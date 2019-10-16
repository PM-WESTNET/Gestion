<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use dosamigos\chartjs\ChartJs;
use yii\jui\DatePicker;
use yii\bootstrap\Collapse;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Tickets report');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="customer-index">
        <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                    'content' => $this->render('_report-filters', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);?>

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
                                'label' => \Yii::t('app', 'Ticket quantity'),
                                'backgroundColor' => "#84C9F1",
                                'borderColor' => "#178DD0",
                                'pointBorderColor' => '#178DD0',
                                'pointBackgroundColor' => '#178DD0',
                                'data' => $data,
                                'fill'=> true,
                            ],
                        ]
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>