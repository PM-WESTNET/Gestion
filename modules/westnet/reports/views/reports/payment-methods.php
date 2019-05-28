<?php

use app\components\companies\CompanySelector;
use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use dosamigos\chartjs\ChartJs;
use yii\jui\DatePicker;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Payment Methods');
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
    <div class="row"><div class="col-md-12">&nbsp;</div></div>

    <div class="row">
        <div class="col-md-12 text-center">
            <?= ChartJs::widget([
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
                    'labels' => $labels,
                    'datasets' => $datasets
                ]
            ]);
            ?>

        </div>
        <div class="row">
            <?= Html::tag( 'h3', Yii::t('app', 'Total'))?>
        </div>
        <div class="row">
            <?php
            foreach ($payments as $item) { ?>
                <label> <?= $item['payment_name'] . ' : '.Yii::$app->formatter->asCurrency($item['facturado']) . ' ('. $item['pagos'] . ' pagos)' ?></label> <br>
            <?php } ?>
            <img >
        </div>

    </div>
    
</div>