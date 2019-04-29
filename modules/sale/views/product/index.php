<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use app\components\helpers\UserA;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\ProductSearch $searchModel
 */

$this->title = Yii::t('app', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?php 
            if (Yii::$app->getModule('accounting')){            
                echo UserA::a("<span class='glyphicon glyphicon-piggy-bank'></span> " . Yii::t('westnet', 'Commission Types'),
                    ['/westnet/product-commission/index'],
                    ['class' => 'btn btn-default']);
            
            }; ?>
            
            <?= UserA::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Product'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
            <?= UserA::a('<span class="glyphicon glyphicon-usd"></span> '.Yii::t('app', 'Update prices', [
                'modelClass' => Yii::t('app','Product'),
            ]), ['update-prices'], ['class' => 'btn btn-info']) ?>
            <?= UserA::a('<span class="glyphicon glyphicon-arrow-up"></span> '.Yii::t('app', 'Stock Income'), ['stock'], ['class' => 'btn btn-info']) ?>
        </p>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
            <div class="input-group">
                <span class="input-group-addon"><?= Yii::t('app','Search code') ?></span>
                <?= Html::activeTextInput($searchModel, 'search_text',['class'=>'filter form-control','id'=>'search_text']) ?>
                <span class="btn input-group-addon" id="resetSearchBox">&times;</span>
            </div> 
            <span class="hint-block search-text-block"><?= Yii::t('app','Please, bring the reader to the barcode or type any search text and press enter.') ?></span>

        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-sm-12">
            <?= app\components\companies\CompanySelector::widget(['model' => $searchModel, 'attribute' => 'stock_company_id', 'label' => Yii::t('app', 'Stock by company'), 'inputOptions' => ['prompt' => Yii::t('app', 'All'), 'class' => 'form-control filter']]) ?>
        </div>
    </div>
            
    
    <?php 
    
    $columns = require(__DIR__.'/columns/_index.php');
            
    $exportColumns = [
        ['class' => 'yii\grid\SerialColumn'],
//      ['class' => yii\grid\CheckboxColumn::className()],
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
//        [
//            'attribute'=>'balance',
//            'filter'=>false,
//            'label'=>Yii::t('app', 'Total stock')
//        ],
        //Para stock de diferentes empresas, usamos stock_company_id
        //Para stock de diferentes empresas, usamos stock_company_id
        [
            'filter'=>false,
            'label'=>Yii::t('app', 'Company Stock'),
            'visible'=>$searchModel->stock_company_id != null,
            'value' => function($model){
                $secondaryStock = $model->getSecondaryStock(null, true);
                if($secondaryStock){
                    return $model->getStock(null, true).' | '.$secondaryStock;
                }
                return $model->getStock(null, true);
            }
        ],
        [
            'filter'=>false,
            'label'=>Yii::t('app', 'Avaible Stock'),
            'visible'=>$searchModel->stock_company_id != null,
            'value' => function($model){
            
                $secondaryAvaibleStock = $model->getSecondaryAvaibleStock(null, true);
            
                if($secondaryAvaibleStock){
                    return $model->getAvaibleStock(null, true).' | '.$secondaryAvaibleStock;
                }
                
                return $model->getAvaibleStock(null, true);
            }
        ],
        [
            'label' => Yii::t('app', 'Stock by company'),
            'visible'=>$searchModel->stock_company_id == null && Yii::$app->params['companies']['enabled'],
            'format'=>'html',
            'value'=>function($model){
                $table = '<table class="table">';
                $companies = app\modules\sale\models\Company::find()->where(['status' => 'enabled'])->all();
                foreach ($companies as $company){
                    //Stock primario
                    $stock = $model->getStock($company, true);
                    //Stock secundario
                    $secondaryStock = $model->getSecondaryStock($company, true);
                    if($secondaryStock){
                        $stock .= " | $secondaryStock";
                    }
                    //Fila
                    $table .= "<tr><td style='padding: 0 10px 0 0;'>$company->name:</td> <td style='padding: 0; white-space: nowrap;'>$stock</td></tr>";
                }
                $table .= '</table>';
                
                return $table;
            }
        ],
        [
            'label' => Yii::t('app', 'Avaible Stock'),
            'visible'=>$searchModel->stock_company_id == null && Yii::$app->params['companies']['enabled'],
            'format'=>'html',
            'value'=>function($model){
                $table = '<table class="table">';
                $companies = app\modules\sale\models\Company::find()->where(['status' => 'enabled'])->all();
                foreach ($companies as $company){
                    //Stock dispoible
                    $avaibleStock = $model->getAvaibleStock($company, true);
                    //Stock secundario disponible
                    $secondaryAvaibleStock = $model->getSecondaryAvaibleStock($company, true);
                    if($secondaryAvaibleStock){
                        $avaibleStock .= " | $secondaryAvaibleStock";
                    }
                    //Fila
                    $table .= "<tr><td style='padding: 0 10px 0 0;'>$company->name:</td> <td style='padding: 0; white-space: nowrap;'>$avaibleStock</td></tr>";
                }
                $table .= '</table>';
                
                return $table;
            }
        ],
        //Formato:
        [
            'label'=>$searchModel->getAttributeLabel('net_price'),
            'attribute'=>'netPrice',
            'format'=>['currency']
        ],
        [
            'label'=>$searchModel->getAttributeLabel('final_price'),
            'attribute'=>'finalPrice',
            'format'=>['currency']
        ],
        'description:ntext',
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
        ],
    ];
            
            
    // Renders a export dropdown men
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $exportColumns,
        'showConfirmAlert'=>false
    ]);
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
    
    <?php
    $grid = GridView::begin([
        'id'=>'grid',
        'tableOptions'=> ['class'=>'table-products'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector'=>'.filter',
        'export'=>false,
        'responsive'=>false,
        'options' => ['class' => 'table-responsive'],                
        'columns' => $columns,
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
            this.focusin = function(){
            }
            
            //public
            this.focusout = function(){
                
                autoFocus = false;
                
            }
            
            this.clear = function(){

                window.location = '<?= yii\helpers\Url::toRoute(['product/index']) ?>';
                
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
    
    <?php $this->registerJs('$("#search_text").focus();'); ?>
    <?php $this->registerJs('$("#search_text").on("focusout",function(){ Search.focusout(); });'); ?>
    <?php $this->registerJs('$("#search_text").on("focusin",function(){ Search.focusin(); });'); ?>
    <?php $this->registerJs('$("#resetSearchBox").on("click",function(){ Search.clear(); });'); ?>
    <?php $this->registerJs('$(window).on("keypress",function(e){ Search.windowKeypress(e); });'); ?>
    <?php $this->registerJs('$(window).on("keyup",function(e){ if(e.which == 27) { Search.clear(); } });'); ?>
    
    <?php 
    if(Yii::$app->params['inteligent_placeholder']['product']['search_text'])
        $this->registerJs('$("#search_text").on("focusin",function(){Search.iPlaceholder()});'); 
    ?>
    
    
</div>
