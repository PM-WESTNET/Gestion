<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use app\modules\sale\models\BillType;
use yii\bootstrap\Modal;
use app\components\helpers\Inflector;
use yii\bootstrap\Collapse;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\modules\sale\models\Bill;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\BillSearch $searchModel
 */

$this->title = Yii::t('app', 'Bills');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('.inactive{opacity: 0.8; font-style: italic;}');
?>
<div class="bill-index">

    <h1>
        <?= Html::encode($this->title) ?>
        <?php
        /**
         * Si se esta aplicando el filtro por tipo de comprobante, 
         * mostramos los nombres de los comprobantes filtrados
         */
        $types = $searchModel->billTypes;
        if($types):
        ?>
        <small>
            <?php 
            //Utilizamos inflector para mostrar en plural
            $inflector = Inflector::getInflector();
            
            foreach($types as $i=>$type) { 
                $name = $inflector->pluralize($type->name);
                echo ($i > 0) ? ", $name" : $name; 
            } ?>
        </small>
        <?php endif; ?>
    </h1>
    
    <?php if($searchModel->customer): ?>
    <h4 class="margin-top-half  margin-bottom-half">
        <span class="font-light-gray font-s"><?= Yii::t('app', 'Customer') ?>:</span> <span class="font-bold"> <?= $searchModel->customer->fullName; ?></span>
    </h4>
    <?php endif; ?>
    <!--this is the filter dropdown menu -->
    <?= Collapse::widget([
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                'content' => $this->render('_search', ['model' => $searchModel]),
                'encode' => false,
            ],
        ],
        'options' => [
            'class' => 'hidden-print'
        ]
    ]);
    
    ?>
    
    <!-- <p>
    </p> -->

    <div class="row">
        <!-- Modulo Export -->
        <div class="col-sm-4">
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'date:date',
                    [
                        'attribute'=>'time',
                        'filter'=>false
                    ],
                    [
                        'attribute'=>'amount',
                        'format'=>['currency']
                    ],
                    [
                        'attribute'=>'taxes',
                        'format'=>['currency']
                    ],
                    [
                        'attribute'=>'total',
                        'format'=>['currency']
                    ],
                    [
                        'attribute' => 'bill_type_id',
                        'value' => function($model){ return $model->billType ? $model->billType->name : null; },
                    ],
                    'number',
                    [
                        'header'=>Yii::t('app','Customer'),
                        'value'=>function($model){ if(!empty($model->customer)) return $model->customer->fullName; }
                    ],
 		            [
                        'attribute' => Yii::t('app', 'Customer document number'),
                        'value' => function($model) {
                            return $model->customer ? $model->customer->document_number : '';
                        }
                    ],
                    [
                        'attribute' => Yii::t('app', 'Customer code'),
                        'value' => function ($model) {
                            return $model->customer ? $model->customer->code : '';
                        }
                    ],
                ],
                'showConfirmAlert'=>false
            ]);
            ?>
        </div>
        
       
    </div>
    
    <?php
    //Si se aplica el filtro de bill_types, no se muestra el filtro bill_type_id
    //en el grid, por no se compatibles.
    if($searchModel->bill_types){
        $typeFilter = false;
    }else{
        $typeFilter = ArrayHelper::map(BillType::find()->all(), 'bill_type_id', 'name');
    }
    ?>
    
    <!--this is the table view for all bills -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        
        //Se ocultan los filtros por un error en gridView.js que no permite utilizar filtros de tipo array
        //'filterModel' => $searchModel,
        
        'filterSelector'=>'.filter',
        'export'=>false,
        'rowOptions' => function ($model, $index, $widget, $grid){
            if($model->active){
              return [];
            }else{
              return ['class' => 'inactive'];
            }
        },
        'columns' => [
            [
                'label' => Yii::t('app', 'Company'),
                'value' => function($model){
                    return (isset($model->customer->parentCompany) ? $model->customer->parentCompany->name . " - " : '' ) .
                        ($model->company ? $model->company->name : null);
                },
            ],
            [
                'attribute' => 'bill_type_id',
                'value' => function($model){ return $model->billType ? $model->billType->name : null; },
                'filter' => $typeFilter
            ],
            [
                'attribute' => 'number',
                'value' => function($model) {
                    return $model->status == Bill::STATUS_CLOSED ? $model->number : '';
                },
            ],
            [
                'header'=>Yii::t('app','Customer'),
                'value' => function ($model) {
                    if (!empty($model->customer))
                        return \app\components\helpers\UserA::a($model->customer->fullName, ['customer/view', 'id' => $model->customer->customer_id]);
                },
                'format' => 'raw',
            ],
            [
                'label'=>Yii::t('app','Customer Category'),
                'value' => function ($model) {
                    if (!empty($model->customer)){
                        $customer_cat = ($model->customer->getCustomerCategory());
                        if(!empty($customer_cat)) return ($customer_cat->one()->name);
                    }
                    return 'n/a';
                },
                'format' => 'raw',
            ],
            'date:date',
            [
                'attribute' => 'expiration',
                'value' => function($model){
                    if($model->expiration == date('Y-m-d')){
                        return Html::tag('span', Yii::$app->formatter->asDate($model->expiration), ['style' => 'color: red;']);
                    }else{
                        return Yii::$app->formatter->asDate($model->expiration);
                    }
                },
                'format' => 'html'                
            ],
            [
                'attribute'=>'amount',
                'label' => Yii::t('app', 'Net'),
                'format'=>['currency']
            ],
            [
                'attribute'=>'taxes',
                'format'=>['currency']
            ],
            [
                'attribute'=>'total',
                'format'=>['currency']
            ],
            [
                'header'=>Yii::t('app','Emitted'),
                'filter' => false,
                'value'=>function($model){ return (!empty($model->ein) ? Yii::t('app','Yes') : Yii::t('app','No') );}
            ],
            [
                'attribute' => 'status',
                'value' => function($model){ return Yii::t('app', ucfirst($model->status)); }
            ],
            [
                'attribute'=>'had_error',
                'value' => function($model){ 
                    return ($model->had_error==true)?'Si':'No';
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view} {update} {pdf} {open} {resend} {delete}'.(Yii::$app->params['enable_send_bill_email'] ? "{email}" : "" ),
                'buttons'=>[
                    'update' => function ($url, $model, $key) {
                        return $model->isEditable ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
                    },
                    'pdf' => function ($url, $model, $key) {
                        return ($model->status === 'completed'||$model->status === 'closed' && (empty($model->ein) && $model->billType->invoiceClass == null ) || ($model->billType->invoiceClass !== null && !empty($model->ein) )  ) ? Html::a('<span class="glyphicon glyphicon-print"></span>', yii\helpers\Url::toRoute(['bill/pdf', 'id'=>$key]), ['target'=>"_blank", 'class' => 'btn btn-print']) : '';
                    },
                    'open' => function ($url, $model, $key) {
                        
                        if($model->isOpenable()){
                            return Html::a('<span class="glyphicon glyphicon-repeat"></span>', Url::toRoute(['bill/open', 'id' => $key]), ['title' => Yii::t('app', 'Open'), 'class' => 'btn btn-repeat']);
                        }
                    },
                    'email' => function ($url, $model, $key) {
                        if($model->status === 'closed' && ($model->customer ? trim($model->customer->email) : "" ) !=""){
                            return  Html::a('<span class="glyphicon glyphicon-envelope"></span>', Url::toRoute(['bill/email', 'id' => $key]), ['title' => Yii::t('app', 'Send By Email'), 'class' => 'btn btn-info']);
                        }
                    },
                    'resend' => function ($url, $model, $key) {
                        if($model->class === 'bill' && $model->status === 'closed' && is_null($model->ein) && ($model->billType->invoiceClass!==null) ){
                            return  Html::a('<span class="glyphicon glyphicon-share"></span>',Url::toRoute(['bill/resend', 'id' => $key]), ['title' => Yii::t('app', 'Re-send Invoice'), 'onclick'=>'Search.removeIcon(this)']);
                        }
                    },
                    'delete' => function ($url, $model, $key) {
                        if($model->getDeletable() ){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['bill/delete', 'id'=>$key]), [
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                                'class' => 'btn btn-danger'
                            ]);
                        }
                    }

                ]
            ]
        ],
    ]); ?>
    
    <table class="table table-bordered">
        <tr>
            <td>
                <?= Yii::t('app', 'Total for this period'); ?>
            </td>
            <td>
                <strong>
                    <?php
                    //Formatter para currency
                    $formatter = Yii::$app->formatter;
                    echo $formatter->asCurrency($searchModel->periodTotal); ?>
                </strong>
            </td>
        </tr>
    </table>

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

            $.pjax({container: '#grid', url: '<?= Url::toRoute(['stock-movement/graph']) ?>'});

        }
        
        this.clearDates = function(){
            
            $('.dates').val('');
            $('.dates').change();
            
        }

        this.removeIcon = function(elem) {
            $(elem).remove();
        }

    }

</script>
