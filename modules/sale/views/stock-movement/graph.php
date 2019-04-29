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

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-list-alt"></span> '.Yii::t('app', 'Go to products'), ['product/index'], ['class' => 'btn btn-info']) ?>
            <?php
            //Link a datos sin grafico
            $link_url = ['index'];
            if($product = Yii::$app->request->get('product_id'))
                $link_url['product_id'] = $product;
            echo Html::a(Yii::t('app', 'Data without chart'), $link_url, ['class' => 'btn btn-default']) ?>
        </p>
    </div>
    
    <?php \yii\widgets\Pjax::begin(); ?>
    
    
    <div class="row">
        <div class="col-sm-12">
            <form class="form-inline" role="form">
                
                <?= app\components\companies\CompanySelector::widget(['model' => $searchModel, 'template' => '{label} {input}', 'inputOptions' => ['class' => 'form-control filter', 'prompt' => Yii::t('app', 'All')]]) ?>
                |
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
                    <?= Html::label(Yii::t('app','Chart')); ?>
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
    
    <?= dosamigos\chartjs\ChartJs::widget([
        'type' => $searchModel->chartType,
        'options' => [
            'height' => 600,
            'width' => 1200,
            'class' => 'img-responsive',
        ],
        'data' => [
            'labels' => $graphData->getSteps(),
            'datasets'=>$graphData->getDatasets()
        ]
    ]);
    ?>
    
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
//            [
//                'label'=>Yii::t('app','Type'),
//                'value'=>function($model){
//                    if($model->type == 'in')
//                        return '<span style="color: green;" class="glyphicon glyphicon-arrow-up"></span> '. Yii::t('app','In');
//                    elseif($model->type == 'out')
//                        return '<span style="color: red;" class="glyphicon glyphicon-arrow-down"></span> '. Yii::t('app','Out');
//                },
//                'format'=>'html',
//                'attribute'=>'type',
//                'filter'=>[
//                    'in'=>Yii::t('app','In'),
//                    'out'=>Yii::t('app','Out')
//                ]
//            ],
//            'qty',
            [
                'attribute'=>'date',
                'value'=>function($model,$i,$o){ return Yii::$app->formatter->asDate($model->date,'medium'); }
            ],
            'balance',
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
