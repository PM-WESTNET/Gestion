<?php

use app\components\companies\CompanySelector;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\jui\DatePicker;
use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\widgets\Select2 as WidgetsSelect2;



$model = new \app\modules\sale\models\search\CustomerSearch();
$model->load(Yii::$app->request->getQueryParams());
$this->title = Yii::t('app', 'Customers By Node Historic');


/*@var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */
?>

<div class="customer-index">
    <?php $this->params['breadcrumbs'][] = $this->title; ?>
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            
            <?php 
                $form = ActiveForm::begin(['method'=> 'get', 'id' => 'filterForm', 'action' => ['customers-by-node-historic']]);
                $data = ArrayHelper::map(Node::find()->orderBy('name')->all(),'node_id', 'name');
                // var_dump($model);die();
                echo $form->field($model, 'node_id')->widget(Select2::class, [
                    'data' =>  $data,
                    'options' => ['placeholder' => Yii::t('app','All nodes'), 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]);
            ?>
        <?php ActiveForm::end();?>

        </div>

    </div>

    <div class="customer-search">
        <?php $form = ActiveForm::begin(['method' => 'POST']); ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">


                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::activeLabel($model, 'date_from'); ?>
                    <?= DatePicker::widget([
                        'language' => Yii::$app->language,
                        'attribute' => 'date_from',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options' => [
                            'class' => 'form-control filter dates',
                            'placeholder' => Yii::t('app', 'Date')
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
                        'attribute' => 'date_to',
                        'dateFormat' => 'dd-MM-yyyy',
                        'options' => [
                            'class' => 'form-control filter dates',
                            'placeholder' => Yii::t('app', 'Date')
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
        <?php ActiveForm::end();?>
    </div>
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <?= ChartJs::widget([
                'type' => 'bar',
                'options' => [
                    'width' => 800,
                    'height' => 400,
                ],
                'data' => [
                    // 'labels' => $cols,
                    'datasets' => [
                        [
                            'label' => \Yii::t('app', 'Customers'),
                            'backgroundColor' => "rgba(255, 99, 132, 0.2)",
                            'borderColor' => "rgba(255, 99, 132, 0.2)",
                            'pointStrokeColor' => "#fff",
                            // 'data' => $data,
                        ],
                    ]
                ]
            ]);
            ?>
        </div>

    </div>
    <!-- <div style="padding-top: 20px">
        Html::label(Yii::t('app', 'References')) ?>
        <p>
            Se tienen en cuenta los contratos activos que la fecha de finalización sea menor al dia de la consulta (ultimo dia del mes) o sea nula.
        </p>
    </div> -->