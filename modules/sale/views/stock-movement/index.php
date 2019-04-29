<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\StockMovementSearch $searchModel
 */

$this->title = Yii::t('app', 'Stock movements');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-movement-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-list-alt"></span> '.Yii::t('app', 'Go to products'), ['product/index'], ['class' => 'btn btn-info']) ?>
            <?php
            //Link a datos con grafico
            $link_url = ['graph'];
            if($product = Yii::$app->request->get('product_id'))
                $link_url['product_id'] = $product;
            echo Html::a('<span class="glyphicon glyphicon-blackboard"></span> '.Yii::t('app', 'Data with chart'), $link_url, ['class' => 'btn btn-default']) ?>
        </p>
    </div>

    <!-- <p> -->
        <div class="row">
            <div class="col-sm-12">
                <form class="form-inline" role="form">
                    
                    <div class="col-md-6 no-padding" >
                        <?= app\components\companies\CompanySelector::widget(['model' => $searchModel, 'template' => '{label} {input}', 'labelOptions' => ['class' => 'margin-right-full'] ,'inputOptions' => ['class' => 'form-control filter', 'prompt' => Yii::t('app', 'All')]]) ?>                        
                    </div>                    
                    <div class="form-group col-md-2">
                        <?= Html::activeLabel($searchModel, 'fromDate', ['class'=>'sr-only']); ?>
                        <?php 
                        echo yii\jui\DatePicker::widget([
                            'language' => Yii::$app->language,
                            'model' => $searchModel,
                            'attribute' => 'fromDate',
                            'dateFormat' => 'dd-MM-yyyy',
                            'options'=>[
                                'class'=>'form-control filter dates',
                                'placeholder'=>Yii::t('app','From Date')
                            ]
                        ]);
                        ?>
                        <?php //Html::activeTextInput($searchModel, 'fromDate', ['class'=>'form-control filter','placeholder'=>Yii::t('app','From Date')]); ?>
                    </div>
                    <div class="form-group col-md-2">
                        <?= Html::activeLabel($searchModel, 'toDate', ['class'=>'sr-only']); ?>
                        <?php 
                        echo yii\jui\DatePicker::widget([
                            'language' => Yii::$app->language,
                            'model' => $searchModel,
                            'attribute' => 'toDate',
                            'dateFormat' => 'dd-MM-yyyy',
                            'options'=>[
                                'class'=>'form-control filter dates',
                                'placeholder'=>Yii::t('app','To Date')
                            ]
                        ]);
                        ?>
                        <?php // Html::activeTextInput($searchModel, 'toDate', ['class'=>'form-control filter','placeholder'=>Yii::t('app','To Date')]); ?>
                    </div>
                    <div class="form-group col-md-2 no-padding">
                        <div onclick="Search.clearDates();" class="btn btn-warning float-right"><span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app','Clean dates'); ?></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="clearfix margin-top-full"></div>
    <!-- </p> -->
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => '.filter',
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>Yii::t('app','Company'),
                'value'=>function($model, $key, $index){
                    return $model->company ? $model->company->name : null;
                }
            ],
            [
                'label'=>Yii::t('app','Product'),
                'value'=>function($model, $key, $index){
                    return $model->product->name;
                }
            ],
            [
                'label'=>Yii::t('app','Type'),
                'value'=>function($model){
                
                    $values = [
                        'in' => '<span style="color: green;" class="glyphicon glyphicon-arrow-up"></span> '. Yii::t('app','In'),
                        'out' => '<span style="color: red;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','Out'),
                        'r_in' => '<span style="color: gold;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','R. In'),
                        'r_out' => '<span style="color: orange;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','R. Out')
                    ];
                    
                    return $values[$model->type];
                
                },
                'format'=>'html',
                'attribute'=>'type',
                'filter'=>[
                    'in'=>Yii::t('app','In'),
                    'out'=>Yii::t('app','Out')
                ]
            ],
            'qtyAndUnit',
            [
                'attribute'=>'secondaryQtyAndUnit',
                'visible'=>app\modules\config\models\Config::getValue('enable_secondary_stock')
            ],
            'concept',
            [
                'attribute'=>'date',
                'value'=>function($model,$i,$o){ return Yii::$app->formatter->asDate($model->date); }
            ],
            'time',
            'stock',
            'avaible_stock',
            // 'bill_detail_id',

            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],

        ],
    ]); ?>

</div>

<script type="text/javascript">
    
    var Search = new function(){

        //public
        this.windowKeypress = function(e){

            if($(":focus").length == 0 && e.which > 20 && e.which < 127) {

                autoFocus = true;

                $("#search_text").val(String.fromCharCode( e.which ));
                $("#search_text").focus();

            }

        }
        this.clear = function(){

            $.pjax({container: '#grid', url: '<?= yii\helpers\Url::toRoute(['stock-movement/graph']) ?>'});

        }
        
        this.clearDates = function(){
            
            $('.dates').val('');
            $('.dates').change();
            
        }

    }

</script>
