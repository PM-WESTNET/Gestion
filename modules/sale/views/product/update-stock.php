<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\ProductSearch $searchModel
 */

$this->title = Yii::t('app', 'Stock Income');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Product'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('app', 'Product list', [
                'modelClass' => Yii::t('app','Product'),
            ]), ['index'], ['class' => 'btn btn-info']) ?>
        </p>
    </div>

    <div class="input-group">
        <span class="input-group-addon"><?= Yii::t('app','Search') ?></span>
        <?= Html::activeTextInput($searchModel, 'search_text',['class'=>'filter form-control','id'=>'search_text']) ?>
        <span class="btn input-group-addon" id="resetSearchBox">&times;</span>
    </div> 
    <span class="hint-block search-text-block"><?= Yii::t('app','Please, bring the reader to the barcode or type any search text and press enter.') ?></span>

    <hr>
    
    <div class="row">
        <div class="col-sm-12">
            <?= app\components\companies\CompanySelector::widget(['model' => $searchModel, 'attribute' => 'stock_company_id', 'label' => Yii::t('app', 'Stock by company'), 'inputOptions' => ['class' => 'form-control filter']]) ?>
        </div>
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
    
    <div class="row">
        <div class="col-lg-12">
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
    </div>

    <?php $grid = GridView::begin([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector'=>'.filter',
        'columns' => require(__DIR__.'/columns/_update-stock.php'),
        'rowOptions' => function ($model, $index, $widget, $grid){

            if(Yii::$app->session->get('InputColumnLastUpdateField') == $model->product_id){
                return ['class' => 'success'];
            }else{
                return [];
            }
            
        },
    ]); ?>
    
    <?php $grid->end(); ?>
    
    <div id="hidden-filters"></div>

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
            this.focusout = function(){
                
                autoFocus = false;
                
            }
            
            this.clear = function(){

                window.location = '<?= yii\helpers\Url::toRoute(['product/stock']) ?>';
                
            }
            
            this.addCategory = function(category){
                
                $('#hidden-filters').append('<input class="filter" id="category-filter-'+category+'" type="hidden" name="ProductSearch[categories][]" value='+category+'>');
                $('#<?php echo $grid->id; ?>').yiiGridView('applyFilter');
                
            }
            
            this.removeCategory = function(category){
                
                self.clear();
                
            }

        }
        
    </script>
    
    <?php 
    //Column updater
    \app\assets\ColumnUpdaterAsset::register($this);
    $this->registerJs('ColumnUpdater.init();'); ?>
    
    <?php $this->registerJs('$("#search_text").on("focusout",function(){ Search.focusout(); });'); ?>
    <?php $this->registerJs('$("#search_text").on("focusin",function(){ Search.focusin(); });'); ?>
    <?php $this->registerJs('$("#resetSearchBox").on("click",function(){ Search.clear(); });'); ?>
    <?php $this->registerJs('$(window).on("keypress",function(e){ Search.windowKeypress(e); });'); ?>
    <?php $this->registerJs('$(window).on("keyup",function(e){ if(e.which == 27) { Search.clear(); } });'); ?>
    
    <?php 
    //Selecciona el texto al posicionar el cursor sobre un input de una columna InputColumn
    if(Yii::$app->params['auto_select_input_column'])
        $this->registerJs('$(".input-column").on("mouseup",function(e){$(this).select();});'); 
    ?>
    
    
</div>
