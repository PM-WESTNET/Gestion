<?php

use app\components\helpers\Inflector;
use app\modules\sale\models\BillType;
use app\modules\sale\models\CustomerCategory;
use app\modules\sale\modules\contract\models\Contract;
use yii\bootstrap\Collapse;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var CustomerSearch $searchModel
 */
$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .title{
        margin-bottom: 10px;
    }
</style>
<div class="customer-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?=
            Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Create {modelClass}', [
                        'modelClass' => Yii::t('app', 'Customer'),
                    ]), ['create'], ['class' => 'btn btn-success'])
            ?>
        </p>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Filtros</h4>            
        </div>
        <div class="panel-body">
            <?= $this->render('_filters-customer', ['model' => $searchModel, 'categoriesPlan' => $categoriesPlan]); ?>
        </div>
    </div>

    
    <?php
        if (isset(Yii::$app->request->getQueryParams()['search_text'])) {
            $params=['/sale/customer/export-index', 'search_text'=> $searchModel->search_text ];
        }else{
            $params=  [
                '/sale/customer/export-index', 
                'CustomerSearch[customer_id]'=> $searchModel->customer_id,
                'CustomerSearch[company_id]' => $searchModel->company_id,
                'CustomerSearch[zone_id]' => $searchModel->zone_id,
                'CustomerSearch[customer_category_id]' => $searchModel->customer_category_id,
                'CustomerSearch[customer_class_id]' => $searchModel->customer_class_id,
                'CustomerSearch[node_id]' => $searchModel->node_id,
                'CustomerSearch[plan_id]' => $searchModel->plan_id,
                'CustomerSearch[customer_status]' => $searchModel->customer_status,
                'CustomerSearch[connection_status]' => $searchModel->connection_status,
                'CustomerSearch[contract_status]' => $searchModel->contract_status,                   
                'CustomerSearch[email_status]' => $searchModel->email_status,
                'CustomerSearch[email2_status]' => $searchModel->email2_status,
                'CustomerSearch[mobile_app_status]' => $searchModel->mobile_app_status,
                ];
            }
        echo Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('app', 'Export'), Url::to($params), ['class'=> 'btn btn-warning', 'target' => '_blank']);
    
    
    ?>
    
    
    
    <?php Pjax::begin(); ?>

    <?php
    $columns = [
        [
            'label' => Yii::t('app', 'Customer Number'),
            'value' => 'code'
        ],
        [
            'label' => Yii::t('app', 'Lastname') . ' y '. Yii::t('app', 'Name'),
            'value' => function ($model){
                return $model->fullName;
            }
        ],
        'document_number',
        [
            'label' => 'Foto doc.',
            'value' => function($model) {
                $html_view = Html::a('<span class="glyphicon glyphicon-ok" style="color: darkgreen;"></span>', ['view', 'id' => $model->customer_id], ['target' => '_blank', 'data-pjax' => '0']);
                $html_update = Html::a('<span class="glyphicon glyphicon-remove" style="color: darkred"></span>', ['update', 'id' => $model->customer_id], ['target' => '_blank', 'data-pjax' => '0']);

                return $model->document_image ? $html_view : $html_update;
            },
            'format' => 'raw',
            'headerOptions' => ['max-width' => '70px'],
            'contentOptions' => ['style'=>'text-align: center; max-width:70px'],
        ],
        [
            'label' => 'Foto imp.',
            'value' => function($model) {
                $html_view = Html::a('<span class="glyphicon glyphicon-ok" style="color: darkgreen;"></span>', ['view', 'id' => $model->customer_id], ['target' => '_blank', 'data-pjax' => '0']);
                $html_update = Html::a('<span class="glyphicon glyphicon-remove" style="color: darkred"></span>', ['update', 'id' => $model->customer_id], ['target' => '_blank', 'data-pjax' => '0']);

                return $model->tax_image ? $html_view : $html_update;
            },
            'format' => 'raw',
            'headerOptions' => ['max-width' => '70px'],
            'contentOptions' => ['style'=>'text-align: center; max-width:70px'],
        ],
        'phone',
    ];

    if (Yii::$app->params['category_customer_required']) {
        $columns[] = [
            'attribute' => 'category',
            'content' => function($model, $key, $index, $column) {
                return $model->customerCategory ? $model->customerCategory->name : null;
            },
            'filter' => ArrayHelper::map(CustomerCategory::find()->all(), 'customer_category_id', 'name'),
            'header' => Yii::t('app', 'Customer Category')
        ];
    }

    $columns[] = [
        'attribute' => 'class',
        'content' => function($model, $key, $index, $column) {
            return $model->customerClass ? $model->customerClass->name : null;
        },
        'header' => Yii::t('app', 'Customer Class')
    ];

    // Columna de conexiones. Muestra un listado de las conexiones del cliente y sus estados.    
    $columns[] = [
        'class' => '\yii\grid\DataColumn',
        'content' => function ($model) {
            $connections = '<ul>';
            $contracts = Contract::findAll(['customer_id' => $model->customer_id]);
            if ($contracts != null) {
                foreach ($contracts as $contr) {
                    $conn = \app\modules\westnet\models\Connection::findOne(['contract_id' => $contr->contract_id]);
                    if ($conn != null) {
                        $connections .= '<li><a href="'. Url::to(['/sale/contract/contract/view', 'id' => $contr->contract_id]) . '">'.
                                ($conn->contract->address ? $conn->contract->address->fullAddress: '') .
                                ': ' . Yii::t('app', ucfirst($conn->status_account) . ' Account') . '</a></li>';
                    }else{
                        $connections .= '<li><a href="'. Url::to(['/sale/contract/contract/view', 'id' => $contr->contract_id]) . '">'.
                                ($contr->address ? $contr->address->fullAddress : '') .
                                ': ' . Yii::t('app', 'Without Connection') . '</a></li>';
                    }
                }
                $connections = $connections . '</ul>';
                return $connections;
            } else {
                return ' ';
            }
        },
                'format' => 'html',
                'header' => Yii::t('app', 'Connections')
            ];

            //Columna de empresa, solo si se encuentra activa la func. de empresas
            if (Yii::$app->params['companies']['enabled']) {
                $columns[] = ['class' => 'app\components\companies\CompanyColumn'];
            }

            if (Yii::$app->getModule('checkout')) {
                $columns[] = [
                    'class' => '\yii\grid\DataColumn',
                    'content' => function($model, $key, $index, $column) {
                        return Html::a('<span class="glyphicon glyphicon-usd"></span> ' . Yii::t('app', 'Account'), ['/checkout/payment/current-account', 'customer' => $model->customer_id], ['class' => 'btn btn-sm btn-default']);
                    },
                            'format' => 'html',
                            'contentOptions' => ['class' => 'hidden-print'],
                            'headerOptions' => ['class' => 'hidden-print'],
                        ];
                    }


                    //Colocamos estas dos lineas fuera del partial para ejecutarlo solo una vez
                    //Mostramos una lista de links a lista de comprobantes por tipo y cliente
                    //Bill types
                    $billTypes = BillType::find()->orderBy(['class' => SORT_ASC, 'name' => SORT_ASC])->all();
                    $inflector = Inflector::getInflector();

                    //Columna desplegable con lista de tipos de comprobante
                    /**
                     * Disponible en vista. En lista tarda mucho en cargar, porque se ejecutan muchas consultas
                     * $columns[] = [
                      'class' => '\yii\grid\DataColumn',
                      'content' => function($model, $key, $index, $column) use ($billTypes, $inflector) {

                      return $this->render('_bills-dropdown', ['model' => $model, 'billTypes' => $billTypes, 'inflector' => $inflector]);
                      },
                      'format' => 'html',
                      'visible' => Yii::$app->params['dropdown-operations-list'],
                      'contentOptions' => ['class' => 'hidden-print'],
                      'headerOptions' => ['class' => 'hidden-print'],
                      ];
                     *
                     */
                    $columns[] = ['class' => 'app\components\grid\ActionColumn'];

                    $grid = GridView::begin([
                                'dataProvider' => $dataProvider,
                                'id' => 'grid',
                                //'filterSelector' => '.filter',
                                'options' => ['class' => 'table-responsive'],
                                'columns' => $columns,
                    ]);
                    ?>

                    <?php $grid->end(); ?>

                    <?php Pjax::end(); ?>

                    <script type="text/javascript">

                        var Search = new function () {

                            //private
                            var autoFocus = false;

                            //public
                            this.windowKeypress = function (e) {

                                if ($(":focus").length == 0 && e.which > 20 && e.which < 127) {

                                    autoFocus = true;

                                    $("#search_text").val(String.fromCharCode(e.which));
                                    $("#search_text").focus();

                                } else {
                                    if (e.which === 13) {
                                        if ($('#search_text').val() !== '') {
                                            location.href = '<?= Url::to(['customer/index']) ?>' + '&search_text=' + $('#search_text').val();
                                        } else {
                                            $('#filterButton').click();
                                        }
                                    }
                                }

                            }

                            //public
                            this.iPlaceholder = function () {
                                $("#search_text").attr("placeholder", $("#search_text").val());
                                $("#search_text").val("");
                            }

                            //public
                            this.focusin = function () {

                                if (autoFocus == false) {
                                    $(".search-text-block").show(300);
                                }

                            }

                            //public
                            this.focusout = function () {

                                autoFocus = false;
                                $(".search-text-block").hide(300);

                            }

                            this.clear = function () {

                                $.pjax({container: '#<?php echo $grid->id; ?>', url: '<?= Url::toRoute(['customer/index']) ?>'});

                            }

                            this.clearSearchText = function () {
                                $('#search_text').val('');
                                $('#search_text').attr('placeholder', '');
                                $.each($('#filterForm input[type="hidden"]'), function(i,o){
                                    if ($(o).attr('name') !== "r") {
                                        $(o).val('');
                                    }
                                    
                                });
                            }

                            this.clearFilters = function () {
                                $.each($('#filterForm input'), function (i, o) {
                                    if ($(o).attr('type') === 'text') {
                                        $(o).val('');
                                    }
                                });
                                $('#customersearch-company_id').val('');
                                $('#customersearch-customer_category_id').val('');
                                $('#customersearch-customer_class_id').val('');
                                $('#customersearch-node_id ').val('');
                                $('#plan_id').val('');
                                $('#geocode').val('');
                                $('#customersearch-zone_id').select2('val', '');
                                
                                $.each($('#filterForm input[type="checkbox"]'), function(i,o){
                                    if($(o).is(':checked')){
                                        $(o).prop('checked', false);
                                    }
                                });
                                
                                $.each($('#filterForm input[type="hidden"]'), function(i,o){
                                    if ($(o).attr('name') !== "r") {
                                        $(o).val('');
                                    }
                                    
                                });

                            }

                        }

                    </script>

                <?php $this->registerJs('$("#search_text").focus();'); ?>
                <?php $this->registerJs('$("#resetSearchBox").on("click",function(){ Search.clear(); });'); ?>
                <?php $this->registerJs('$(window).on("keypress",function(e){ Search.windowKeypress(e); });'); ?>
                <?php $this->registerJs('$(window).on("keyup",function(e){ if(e.which == 27) { Search.clear(); } });'); ?>
                <?php $this->registerJs('$(document).on("click", ".filters-costumer", function(){Search.clearSearchText();})'); ?>
                    <?php $this->registerJs('$(document).on("click", "#search_text", function(){Search.clearFilters();})'); ?>

                    <?php
                    if (Yii::$app->params['inteligent_placeholder']['customer']['search_text'])
                        $this->registerJs('$("#search_text").on("focusin",function(){Search.iPlaceholder()});');
                    ?>

</div>
<?php
$this->registerJs('
$("#customersearch-customer_id").on("select2:select", function (e) {
    $("#filterButton").trigger("click");
});
');
?>
