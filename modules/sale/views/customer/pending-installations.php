<?php

use app\modules\westnet\models\Node;
use app\modules\westnet\notifications\models\search\CustomerSearch;
use kartik\export\ExportMenu;
use yii\bootstrap\Collapse;
use yii\bootstrap\Html as Html2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var CustomerSearch $searchModel
 */
$this->title = Yii::t('app', 'Pending Installations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">
    
    <div id='message'></div>
    
    <div id="printAdsDiv" style="display:none;">
       
    </div>

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>        
    </div>

    <div class="pendings-search">

        <?php
            $item = '<span class="glyphicon glyphicon-chevron-down"></span> ' . Yii::t('app', 'Filters');

            echo Collapse::widget([
                'items' => [
                    [
                        'label' => $item,
                        'content' => $this->render('_pendings-instalations-filters', ['model' => $searchModel]),
                        'encode' => false,
                    ],
                ],
                'options' => [
                    'class' => 'print',
                    'aria-expanded' => 'false'
                ]
            ]);
        ?>
    </div>

    <p>
        <a href="#" id="printAds" class="btn btn-info"><span class="glyphicon glyphicon-print"></span><?= Yii::t('app', 'Print ADS By Batch')?></a>
        <a href="#" id="setNode" class="btn btn-warning"><span class="glyphicon glyphicon-menu-right"></span><?= Yii::t('app', 'Set Tentative Node')?></a>
    </p>  


    <?php
    echo ExportMenu::widget([
        'dataProvider' => $ads,
        'columns' => [
            [
                'label'=> Yii::t('app', 'Customer Number'),
                'value'=>function($model){
                    return  $model->customer->code;
                },
                'contentOptions' => ['style' => 'text-align: left'],
            ],
            [
                'label'=> Yii::t('app', 'Customer'),
                'value'=>function($model){
                    return  $model->customer->fullName;
                },
                'contentOptions' => ['style' => 'text-align: left'],
            ],
            [
                'label'=> Yii::t('app', 'Email'),
                'value'=>function($model){
                    return  $model->customer->email;
                }
            ],
            [
                'label'=> Yii::t('app', 'Phones'),
                'value'=>function($model){
                    return  $model->customer->phone;
                }
            ]
        ],
    ]);

    $columns = [
        [
            'class'=> \yii\grid\CheckboxColumn::className(),
            'checkboxOptions' => function($model){
                $nodeSet= (empty($model->tentative_node) ? 0 : 1);
                return ['value' => $model->contract_id . '-'. $nodeSet];
            }
        ],
        [
            'label' => Yii::t('app', 'Contract'),
            'attribute' => 'contract_id',
            'value' => function($model) {
                return $model->contract_id;
            },
        ],
        [
            'label' => Yii::t('app', 'Date'),
            'attribute' => 'date',
            'value' => function($model) {
                return $model->date;
            },
        ],
        [
            'label' => Yii::t('app', 'Customer Number'),
            'attribute' => 'c.code',
            'value' => function($model) {
                return $model->customer->code;
            },
        ],
        [
            'label' => Yii::t('app', 'Customer'),
            'attribute' => 'c.name',
            'value' => function($model) {
                return $model->customer->fullName;
            },
        ],
        [
            'label' => Yii::t('app', 'Address'),
            'value' => function($model) {
                return $model->address->fullAddress;
            },
        ],
        [
            'label' => Yii::t('app', 'Tentative Node'),
            'attribute'=> 'tentative_node',
            'value' => function($model) {
                $tentative_node = Node::findOne(['subnet' => $model->tentative_node]); 
                return (empty($tentative_node) ? null : $tentative_node->name);
            },
        ],
        [
            'label' => Yii::t('westnet', 'Vendor'),
            'attribute'=> 'vendor_id',
            'value' => function($model) {
               return (empty($model->vendor) ? null : $model->vendor->fullName);
            },
        ],
        
        [
            'class' => 'app\components\grid\ActionColumn',
            'buttons' => [
                'view' =>function($url, $model, $key){
                    return Html2::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['contract/contract/view']). '&id=' . $model->contract_id, ['class' => 'btn btn-view']);
                },
            ],
            'template' => '{view}',        
        ],
     ];

    $grid = GridView::begin([
            'dataProvider' => $ads,
            'id' => 'grid',
            'options' => ['class' => 'table-responsive'],
            'columns' => $columns,
            'rowOptions' => function($model){
                $nodeSet= (empty($model->tentative_node) ? 0 : 1);
                return ['data' =>[ 'key' => $model->contract_id . '-'. $nodeSet]];
            }
    ]);
            ?>

    <?php $grid->end(); ?>
</div>

<div class="modal fade" id="set-node-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Select Node') ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <label><?= Yii::t('westnet', 'Node') ?></label>
                    <select id="node_id" class="form-control"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-primary" id="set-node-button"><?php echo Yii::t('app', 'Set Tentative Node') ?></button>
            </div>
        </div>
    </div>
</div>

<script>

    var pendingInstalationsIndex= new function(){
        
        this.init= function(){
            $(document).on('click', '#set-node-button', function(e){
               e.preventDefault();
               pendingInstalationsIndex.setNode();
            });
            
            $(document).on('click', '#setNode', function(e){
               e.preventDefault();
               $('#set-node-modal').modal();
            });
            
            $(document).on('click', '#printAds', function(e){
                e.preventDefault();
                pendingInstalationsIndex.printAds();
            });
            pendingInstalationsIndex.getNodes();
        };
        
        
        this.printAds= function(){
            var selection= $('#grid').yiiGridView('getSelectedRows');
            if(pendingInstalationsIndex.validateForPrint(selection)){
                var contracts= pendingInstalationsIndex.getContracts(selection);
                var form= document.createElement('form');
                $(form).attr('id', 'adsForm');
                $(form).attr('action', '<?= Url::to(['/westnet/ads/print-ads-by-batch'])?>');
                $(form).attr('method', 'POST');
                $(form).attr('target', '_blank');
                $(form).append('<input type="text" name="_csrf" value="' + '<?=  Yii::$app->request->getCsrfToken()?>' + '">');
                if(contracts.length > 0){
                    $.each(contracts, function(i, id){
                        $(form).append('<input type="text" name="contracts['+ i +']" value="' + id + '">');
                    });
                
                    $('#printAdsDiv').append(form);
                    $(form).submit();
                    $(form).remove();
                    pendingInstalationsIndex.cleanSelection();
                
                }else{                
                    $('#message').html('<div class="alert alert-danger">' +  '<?= Yii::t('app', 'You must select at least a option')?> ' + '</div>');
                }
            }else{
                $('#message').html('<div class="alert alert-danger">' +  '<?= Yii::t('app', 'All contracts selected must have defined the tentative node')?> ' + '</div>');
            }
        };
        
        
        this.setNode= function(){
            var selection= $('#grid').yiiGridView('getSelectedRows');
            var contracts= pendingInstalationsIndex.getContracts(selection);
            if(contracts.length > 0){
                $.ajax({
                    url: '<?= Url::to(['/sale/contract/contract/set-tentative-node']) ?>',
                    data: {contracts: contracts, tentative_node: $('#node_id').val()},
                    method: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if (data.status === 'success') {
                            location.reload();       
                        }else{
                            $('#message').html('<div class="alert alert-danger">' +  data.message + '</div>');
                            $('#set-node-modal').modal('hidden');
                        }
                   }
                });
            }else{
                $('#message').html('<div class="alert alert-danger">' +  '<?= Yii::t('app', 'You must select at least a option')?> ' + '</div>');
                $('#set-node-modal').modal('hide');
            }
        };
        
        this.getNodes= function(){
            $.ajax({
                url : "<?= \yii\helpers\Url::to(['/westnet/node/all-nodes'])?>",
                method: "post",
                dataType: "json",
                success: function(data){
                    $.each(data, function(i,n){
                        $('#node_id').append('<option value="'+n.subnet+'">'+n.name+'</option>');
                        $('#tentative_node_filter').append('<option value="'+n.subnet+'">'+n.name+'</option>');
                    });
                    
                    
                }
            });
        };
        
        this.validateForPrint= function(selection){
            var valid= true;
            console.log(selection);
            for(var i= 0; i< selection.length; i++){                
                var s= selection[i].toString().split('-');
                var setNode= s[1];
                console.log(s);
                
                if (setNode === '1') {
                   valid= true;                   
                }else{
                    valid= false;
                    break;
                }
            }
            
            return valid;           
        }
        
        this.getContracts= function(selection){
            var contracts=[];
            $.each(selection, function(i, s){
                s= s.toString().split('-');
                var id= s[0];
                contracts.push(id);                  
            });
            
            return contracts;
        }
        
        this.cleanSelection= function(){
            $('input [type="checkbox"]').each(function(i, ch){
                if ($(ch).is(':checked')) {
                    $(ch).attr('checked', false);
                }
            });
        }
    }
</script>

<?php $this->registerJs('pendingInstalationsIndex.init()');?>