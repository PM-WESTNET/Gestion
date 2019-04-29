<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\PlanSearch $searchModel
 */
$reflex = new \ReflectionClass($searchModel); 
$nameSearch=$reflex->getShortName();


if($nameSearch==='ProductSearch'){
    $this->title = Yii::t('app', 'Product prices');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}
else if($nameSearch==='PlanSearch'){
    $this->title = Yii::t('app', 'Plan prices');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plans'), 'url' => ['plan/index']];
    $this->params['breadcrumbs'][] = $this->title;
}
?>

<div class="product-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Plan'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
            <?php 
            if($nameSearch==='ProductSearch'){
                echo Html::a(Yii::t('app', 'Product list', ['modelClass' => Yii::t('app','Product'),]), ['index'], ['class' => 'btn btn-info']) ;
            }
            else if($nameSearch==='PlanSearch'){
                echo Html::a(Yii::t('app', 'Plan list', ['modelClass' => Yii::t('app','Plan'),]), ['plan/index'], ['class' => 'btn btn-info']) ;
            }
            ?>
        </p>
    </div>

    <div class="input-group">
        <span class="input-group-addon"><?= Yii::t('app','Search text') ?></span>
        <?= Html::activeTextInput($searchModel, 'search_text',['class'=>'filter form-control','id'=>'search_text']) ?>
        <span class="btn input-group-addon" id="resetSearchBox">&times;</span>
    </div> 
    <span class="hint-block search-text-block"><?= Yii::t('app','Please, bring the reader to the barcode or type any search text and press enter.') ?></span>

    <hr>
    
    <?php \yii\widgets\Pjax::begin(); ?>
    
    <div class="pull-right">
    <?php
    if(!empty($searchModel->categories)){
        echo Yii::t('app','Category filters').': ';
        $i18n_title = Yii::t('app', 'Category').':';
        foreach($searchModel->categories as $i=>$category){
            $category = \app\modules\sale\models\Category::findOne($category);
            echo "<a style='margin-bottom: 10px;' class='btn btn-primary btn-sm' onclick='Search.removeCategory($category->category_id);'><span class='glyphicon glyphicon-remove'></span> $i18n_title $category->name </a>";
        }
    }
    ?>
    </div>
    
    <?php
    //Con flash no funciona porque el mensaje es destruido en el request de pjax (a pesar de tener el 3 parametro como true). TODO: implementacion para Session alternativa
    if(is_array(Yii::$app->session->get('updater-messages')) ){
        foreach(Yii::$app->session->get('updater-messages') as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div>';
        }
        Yii::$app->session->remove('updater-messages');
    }
    ?>

    <?php $grid = GridView::begin([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector'=>'.filter',
        'options' => ['class' => 'table-responsive'],                
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => yii\grid\CheckboxColumn::className()],
            //'product_id',
            [
                'attribute'=>'name',
                'content' => function($model, $key, $index, $column){
                    //Nombre
                    $content = $model->name;
                    
                    if(Yii::$app->params['categories-location'] == 'name'){
                        $content .= '<br/>';
                        foreach($model->categories as $i=>$category){
                            if($i > 0){
                                $content .= ', ';
                            }
                            $content .= "<a href='#' onclick='Search.addCategory($category->category_id)'>$category->name</a>";
                            //$content .= Html::a($category->name, ['product/index','ProductSearch'=>['categories'=>[$category->category_id] ] ] );
                        }
                    }
                    return $content;
                },
            ],
            [
                'attribute'=>'code',
                'visible'=>Yii::$app->params['show-code-column']
            ],
            //Formato:
            [
                'class' => 'app\components\grid\InputColumn',
                'label'=>$searchModel->getAttributeLabel('netPrice'),
                'attribute'=>'netPrice',
                'format'=>['decimal',2],
                'action'=>'plan/update-price',
                'onUpdate'=>'function(){$.pjax({container: "#grid"});}',
                'onError'=>'function(){alert("Error")}',
                'template'=>'<div class="input-group" style="width: 150px;"><span class="input-group-addon"> $ </span> {input}</div>',
            ],
            [
//                'class' => 'app\components\grid\InputColumn',
//                'label'=>$searchModel->getAttributeLabel('finalPrice'),
                'attribute'=>'finalPrice',
                'format'=>['currency'],
//                'action'=>'product/update-price',
//                'onUpdate'=>'function(){$.pjax({container: "#grid"});}',
//                'onError'=>'function(){alert("Error")}',
//                'template'=>'<div class="input-group" style="width: 150px;"><span class="input-group-addon"> $ </span> {input}</div>',
            ],
            [
                'label'=>Yii::t('app','Last price update'),
                'value'=>function($model, $key, $index, $column){
                    $price = $model->activePrice;
                    if(empty($price)) return null;
                    if($price->timestamp < (time()- ((int)Yii::$app->params['update-price-warning']*60*60*24) )){
                        return "<span style='color: orange;'>$price->date</span>";
                    }else{
                        return $price->date;
                    }
                },
                'format'=>'html'
            ],
            [
                'label'=>Yii::t('app','Categories'),
                'content' => function($model, $key, $index, $column){
                    $content = '';
                    foreach($model->categories as $i=>$category){
                        if($i > 0){
                            $content .= ', ';
                        }
                        $content .= Html::a($category->name, ['product/index','ProductSearch'=>['categories'=>[$category->category_id] ] ] );
                    }
                    return $content;
                },
                'visible'=>Yii::$app->params['categories-location'] == 'column' ? true : false
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{price-history}',
                'buttons' => [
                    'price-history'=>function($url, $model){
                        return Html::a('<span class="glyphicon glyphicon-tags"></span>', $url, [
                            'title' => Yii::t('yii', 'Price history'),
                            'target'=>'_blank',
                            'data-pjax' => '0',
                        ]);
                    }
                ],
                'header'=>Yii::t('app','History')
            ],
            'description:ntext',
            [
                'class' => 'app\components\grid\ActionColumn',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
    
    <?php $grid->end(); ?>
    
    <?php \yii\widgets\Pjax::end(); ?>
    
    <div id="hidden-filters"></div>
    
    <?php
    
    $item = '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Price updater');
    
    echo \yii\bootstrap\Collapse::widget([
        'items' => [
            // equivalent to the above
            [
                'label' => $item,
                'content' => '<div id="batchUpdater"></div>',
                'encode' => false,
            ],
        ]
    ]);
    
    ?>

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
                
//                if(autoFocus == false){
//                    $(".search-text-block").show(300);
//                }

            }
            
            //public
            this.focusout = function(){
                
                autoFocus = false;
//                $(".search-text-block").hide(300);
                
            }
            
            this.clear = function(){

                $.pjax.reload({container: '#<?php echo $grid->id; ?>', url: '<?= yii\helpers\Url::toRoute(['product/update-prices']) ?>'});
                
            }
            
            this.addCategory = function(category){
                
                $('#hidden-filters').append('<input class="filter" id="category-filter-'+category+'" type="hidden" name="ProductSearch[categories][]" value='+category+'>');
                $('#<?php echo $grid->id; ?>').yiiGridView('applyFilter');
                
            }
            
            this.removeCategory = function(category){
                
                self.clear();
                
//                $('#category-filter-'+category).remove();
//                $('#<?php echo $grid->id; ?>').yiiGridView('applyFilter');
//                
            }

        }
        
    </script>
    
    <?php $this->registerJs('$("#search_text").on("focusout",function(){ Search.focusout(); });'); ?>
    <?php $this->registerJs('$("#search_text").on("focusin",function(){ Search.focusin(); });'); ?>
    <?php $this->registerJs('$("#resetSearchBox").on("click",function(){ Search.clear(); });'); ?>
    <?php $this->registerJs('$(window).on("keypress",function(e){ Search.windowKeypress(e); });'); ?>
    <?php $this->registerJs('$(window).on("keyup",function(e){ if(e.which == 27) { Search.clear(); } });'); ?>
    <?php $this->registerJs('$.get("'.yii\helpers\Url::toRoute('batch-updater').'").done(function(r){$("#batchUpdater").html(r)});'); ?>
    
    <?php 
    if(Yii::$app->params['inteligent_placeholder']['product']['search_text'])
        $this->registerJs('$("#search_text").on("focusin",function(){Search.iPlaceholder()});'); 
    
    //Selecciona el texto al posicionar el cursor sobre un input de una columna InputColumn
    if(Yii::$app->params['auto_select_input_column'])
        $this->registerJs('$(".input-column").on("mouseup",function(e){$(this).select();});'); 
    ?>
    
    
</div>
