<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\ProductPriceSearch $searchModel
 */

$typeProduct=  app\modules\sale\models\Product::findOne($searchModel->product_id)->type;
$this->title = Yii::t('app', 'Price History');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-movement-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
           <?php 
            if($typeProduct==='plan'){
                $link_url = ['plan/price-history'];
                echo Html::a(Yii::t('app', 'Go to plans'), ['plan/index'], ['class' => 'btn btn-info']); 
            }
            else{ 
                $link_url = ['product/price-history'];
                echo Html::a(Yii::t('app', 'Go to products'), ['product/index'], ['class' => 'btn btn-info']) ;
            }
            
            
           ?>
            
            <?php
            //Link a datos sin grafico
            
            if($product = Yii::$app->request->get('product_id'))
                $link_url['id'] = $product;
            echo Html::a(Yii::t('app', 'Data without chart'), $link_url, ['class' => 'btn btn-default']) ?>
        </p>
        
    </div>
    <?php \yii\widgets\Pjax::begin(); ?>
    
    <?= dosamigos\chartjs\ChartJs::widget([
        'type' => $searchModel->chartType,
        'options' => [
            'height' => 600,
            'width' => 1200
        ],
        'data' => [
            'labels' => $graphData->getSteps(),
            'datasets'=>$graphData->getDatasets()
        ]
    ]);
    ?>
    
    <hr/>
    
    <div class="row">
        <div class="col-sm-12">
            <form class="form-inline" role="form">
                <div class="form-group">
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
                <div class="form-group">
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
                <div class="form-group">
                    <div onclick="Search.clearDates();" class="btn btn-warning"><span class="glyphicon glyphicon-remove"></span> <?= Yii::t('app','Clean dates'); ?></div>
                </div>
                <div class="form-group pull-right">
                    <?= Html::activeLabel($searchModel, 'chartType'); ?>
                    <?= Html::activeDropDownList($searchModel, 'chartType', [
                        'Line'=>Yii::t('app','Line'),
                        'Bar'=>Yii::t('app','Bar'),
                        'Radar'=>Yii::t('app','Radar'),
                    ], ['class'=>'form-control filter']); ?>
                </div>
            </form>
        </div>
    </div>
    
    <hr/>
    
    <?php $grid = GridView::begin([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector'=>'.filter',
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>Yii::t('app','Product'),
                'value'=>function($model, $key, $index){
                    return $model->product->name;
                }
            ],
            'net_price',
            [
                'attribute'=>'date',
                'value'=>function($model,$i,$o){ return Yii::$app->formatter->asDate($model->date,'medium'); }
            ],
            [
                'label'=>Yii::t('app','Color'),
                'value'=>function($model,$key,$index){
                    return '<div style="width: 20px; height: 20px; background: rgb('.$model->product->rgb.');"></div>';
                },
                'format'=>'html'
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]);
    
    $grid->end();
                
    ?>
    
    <?php \yii\widgets\Pjax::end(); ?>
    
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

            $.pjax({container: '#<?php echo $grid->id; ?>', url: '<?= yii\helpers\Url::toRoute(['stock-movement/graph']) ?>'});

        }
        
        this.clearDates = function(){
            
            $('.dates').val('');
            $('.dates').change();
            
        }

    }

</script>
