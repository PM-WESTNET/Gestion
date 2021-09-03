<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\sale\models\search\PlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plan-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => 'Plan',]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
             <?= Html::a('<span class="glyphicon glyphicon-usd"></span> '.Yii::t('app', 'Update prices', [
                'modelClass' => Yii::t('app','Plan'),
            ]), Url::toRoute(['plan/update-prices','type'=>'plan']), ['class' => 'btn btn-info']) ?>
            

        </p>
    </div>
    <div class="input-group">
        <span class="input-group-addon"><?= Yii::t('app','Search text') ?></span>
        <?= Html::activeTextInput($searchModel, 'search_text',['class'=>'filter form-control','id'=>'search_text']) ?>
        <span class="btn input-group-addon" id="resetSearchBox">&times;</span>
    </div> 
    <span class="hint-block search-text-block"><?= Yii::t('app','Please, bring the reader to the barcode or type any search text and press enter.') ?></span>

    <hr>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'     =>  Yii::t('app', 'Company'),
                'value' =>  function($model) {
                    return ($model->company ? $model->company->name : Yii::t('app', 'All') );
                }
            ],
            'name',
            'system',
            'code',
            'description:ntext',
            [
                'label'=>$searchModel->getAttributeLabel('netPrice'),
                'attribute'=>'netPrice',
                'format'=>['currency']
            ],
            [
                'label'=>$searchModel->getAttributeLabel('finalPrice'),
                'attribute'=>'finalPrice',
                'format'=>['currency']
            ],
            'quota',
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{price-history}&nbsp;&nbsp;{stock-history}',
                'buttons' => [
                    'price-history'=>function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-tags"></span>', $url, [
                            'title' => Yii::t('yii', 'Price history'),
                            'target'=>'_blank',
                            'data-pjax' => '0',
                        ]);
                    },
                ],
                'header'=>Yii::t('app','History')
            ],
            [
                'attribute'=>'status',
                'filter'=>[
                    'enabled'=>Yii::t('app','Enabled'),
                    'disabled'=>Yii::t('app','Disabled'),
                ],
                'value'=>function($model){return Yii::t('app',  ucfirst($model->status)); }
            ],


            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>
    <script type="text/javascript">
    
        var Search = new function(){

            //private
            var autoFocus = false;
            
            //private
            var self = this;

            //public
            this.windowKeypress = function(e){

                if($(":focus").length == 0 && e.which > 20 && e.which < 127) {
                    
                    autoFocus = true;
                    
                    $("#search_text").val(String.fromCharCode( e.which ));
                    $("#search_text").focus();

                }
                
            }
            
            //public
            this.iPlaceholder = function(){
                $("#search_text").attr("placeholder",$("#search_text").val());
                $("#search_text").val("");
            }
            
            //public
            this.focusin = function(){
                
//               if(autoFocus == false){
//                    $(".search-text-block").show(300);
//                }

            }
            
            //public
            this.focusout = function(){
                
                autoFocus = false;
//                $(".search-text-block").hide(300);
                
            }
     
        }

    </script>
    
    <?php $this->registerJs('$("#search_text").focus();'); ?>
    <?php $this->registerJs('$("#search_text").on("focusout",function(){ Search.focusout(); });'); ?>
    <?php $this->registerJs('$("#search_text").on("focusin",function(){ Search.focusin(); });'); ?>
    <?php $this->registerJs('$("#resetSearchBox").on("click",function(){ Search.clear(); });'); ?>
    <?php $this->registerJs('$(window).on("keypress",function(e){ Search.windowKeypress(e); });'); ?>
    <?php $this->registerJs('$(window).on("keyup",function(e){ if(e.which == 27) { Search.clear(); } });'); ?>
    <?php $this->registerJs("$('#grid').on('pjax:success',function(){alert(last)});"); ?>
    
    <?php 
    if(Yii::$app->params['inteligent_placeholder']['product']['search_text'])
        $this->registerJs('$("#search_text").on("focusin",function(){Search.iPlaceholder()});'); 
    ?>
    
        
    
    
    
    
</div>
