<?php

use app\components\companies\CompanySelector;
use app\modules\westnet\models\search\NodeSearch;
use app\modules\westnet\reports\ReportsModule;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

/* @var $this View */
/* @var $searchModel NodeSearch */
/* @var $searchCustomer app\modules\sale\models\search\ContractSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Historial de cambios de empresas');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="customer-index">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo Yii::t('app','Filters')?></h3>
            </div>
            <div class="panel body">

                <div class="customer-search">
                    <?php $form = ActiveForm::begin(['method' => 'get']); ?>
                    <div class="row">
                    <div class="col-sm-6">
                            <div class="form-group">
                            <?php echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', 
                                ['form' => $form, 'model' => $searchCustomer, 'attribute' => 'customer_id', 'label' => Yii::t('app','Customer')])
                            ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?= CompanySelector::widget([
                                    'model' => $searchModel,
                                    'attribute' => 'company_id',
                                ])?>
                            </div>
                        </div>
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
            </div>
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

                echo GridView::widget([
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
        <div style="padding-top: 20px">
            <?= Html::label(Yii::t('app', 'References'))?>
            <p>
               Importante: <br>
                Los valores en la cuenta EGRESOS están incluidos en el gráfico, ya que no es posible diferenciarlos por empresas
            </p>
        </div>
    </div>