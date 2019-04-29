<?php

use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yiier\chartjs\ChartJs;
use yii\jui\DatePicker;
use kartik\export\ExportMenu;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Ingresos y Egresos');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="customer-index">
        <div class="title">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>

        <div class="customer-search">
            <?php $form = ActiveForm::begin(['method' => 'get']); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $form->field($searchModel, 'date_from')->widget(DatePicker::class,[
                            'language' => Yii::$app->language,
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
                        <?= $form->field($searchModel, 'date_to')->widget(DatePicker::class,[
                            'language' => Yii::$app->language,
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
            <div class="col-md-12">
                <?php
                function calcular($datas, $att) {
                    $sum = 0;
                    foreach($datas as $data) {
                        $sum += $data[$att];
                    }
                    return $sum;
                }
                echo ExportMenu::widget([
                    'dataProvider' => $data,
                    'columns' => [
                        [
                            'label'=> ReportsModule::t('app', 'Type'),
                            'value'=>function($model){
                                return  $model['tipo'];
                            },
                            'contentOptions' => ['style' => 'text-align: left'],
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Description'),
                            'value'=>function($model){
                                return  $model['descripcion'];
                            },
                            'contentOptions' => ['style' => 'text-align: left'],
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Period'),
                            'value'=>function($model){
                                return  $model['periodo'];
                            },
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Charged'),
                            'value'=>function($model) {
                                return  $model['cobrado'];
                            },
                            'format' => 'currency',
                            'contentOptions' => ['class' => 'text-right'],
                            'footer' => Yii::$app->formatter->asCurrency($totalCobrado),
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Payed'),
                            'value'=>function($model) {
                                return  $model['pagado'];
                            },
                            'format' => 'currency',
                            'footer' => Yii::$app->formatter->asCurrency($totalPagado),
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                    ],
                ]);

                echo \kartik\grid\GridView::widget([
                    'dataProvider' => $data,
                    'filterModel' => $searchModel,
                    'showFooter' => true,
                    'columns' => [
                        [
                            'label'=> ReportsModule::t('app', 'Type'),
                            'value'=>function($model){
                                return  $model['tipo'];
                            },
                            'contentOptions' => ['style' => 'text-align: left'],
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Description'),
                            'value'=>function($model){
                                return  $model['descripcion'];
                            },
                            'contentOptions' => ['style' => 'text-align: left'],
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Period'),
                            'value'=>function($model){
                                return  $model['periodo'];
                            },
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Charged'),
                            'value'=>function($model) {
                                return  $model['cobrado'];
                            },
                            'format' => 'currency',
                            'contentOptions' => ['class' => 'text-right'],
                            'footer' => Yii::$app->formatter->asCurrency($totalCobrado),
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Payed'),
                            'value'=>function($model) {
                                return  $model['pagado'];
                            },
                            'format' => 'currency',
                            'footer' => Yii::$app->formatter->asCurrency($totalPagado),
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                    ],
                ]); ?>
            </div>
        </div>

        <div class="row"><div class="col-md-12">&nbsp;</div></div>

        <div class="row"><div class="col-md-12"><?php echo ReportsModule::t('app', 'Movements Without Voucher') ?></div></div>
        <div class="row">
            <div class="col-md-12 text-center">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $movements,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'label'=> ReportsModule::t('app', 'Type'),
                            'value'=>function($model){
                                return  $model['tipo'];
                            },
                            'contentOptions' => ['style' => 'text-align: left'],
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Description'),
                            'value'=>function($model){
                                return  $model['descripcion'];
                            },
                            'contentOptions' => ['style' => 'text-align: left'],
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Period'),
                            'value'=>function($model){
                                return  $model['fecha'];
                            },
                        ],
                        [
                            'label'=> ReportsModule::t('app', 'Amount'),
                            'value'=>function($model){
                                return  $model['monto'];
                            },
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>