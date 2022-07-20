<?php

use app\modules\sale\modules\contract\models\search\ContractDetailSearch;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Node;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */

$this->title = Yii::t('app', 'Contract of') . ": " . $model->customer->fullName . " - " . $model->customer->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['/sale/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Contract Number') . ": " . $model->contract_id;

$customer = $model->customer;
?>

<div class="contract-view">
    <?php if ($model->hasPendingPlanChange()): ?>
        <?php $change = $model->getPendingPlanChange()?>
        <div class="alert alert-warning">
            <h4><?= Yii::t('app','This contract has pending programmed plan change for {date}', ['date' => $change->date])?></h4>
        </div>
    <?php endif; ?>

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p> <?= $this->render('_contract-buttons', [ 'model' => $model, ])?> </p>
    </div>

    <?php $columns = [
        [
            'label' => Yii::t('app', 'Customer'),
            'value' => $customer->fullName
        ],
        [
            'label' => Yii::t('app', 'Company'),
            'value' => function($model) use ($customer){
                $company = $customer->company;
                $parentCompany = $customer->parentCompany;
                if (!$company) {
                    return '';
                }
                $result = $company->name;
                if ($parentCompany) {
                    $result .= " ( " . $parentCompany->name ." )";
                }
                return $result;
            },
        ],
        'customer.code',
        'date',
        'from_date',
        'to_date',
        [
            'label' => $model->getAttributeLabel('status'),
            'value' => Yii::t('app', ucfirst($model->status))
        ],
        [
            'label' => Yii::t('app', 'Address'),
            'format' => 'raw',
            'value' => (isset($model->address) ? $model->address->fullAddress : $customer->address ),
        ],
    ];

    $columns[]= [
        'label' => Yii::t('app', 'Instalation Schedule'),
        'value' => Yii::t('app', Yii::t('app', ucfirst($model->instalation_schedule))),
    ];

    if (Yii::$app->getModule('westnet')) {
        $columns[] = [
            'label' => Yii::t('westnet', 'Vendor'),
            'value' => isset($model->vendor) ? $model->vendor->fullname : '',
        ];
    }
    
    $columns[]= [
        'label' => Yii::t('app', 'Tentative Node'),
        'value' => function($model){
            // if no tentative node is setted
            if(empty($model->tentative_node)) return '-';
            // if no subnet found from that tentative node
            $node = Node::findOne(['subnet' => $model->tentative_node]);
            return ((!empty($node)) ? $node->name : 'ERROR: subnet de nodo tentativo no encontrada');
        }
    ];
    if($model->low_date) {
        $columns[]= [
            'label' => Yii::t('westnet', 'Date of start of low'),
            'value' => (new \DateTime($model->low_date))->format('d-m-Y'),
        ];
        $columns[]=[
            'label' => Yii::t('westnet', 'Reason of low'),
            'value' => $model->lowCategory->name,
        ];
    }
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $columns,
    ]);

    if (Yii::$app->getModule('westnet')) {
        $connection = Connection::findOne(['contract_id' => $model->contract_id]);
        if ($connection) {
            ?>
            <div id="messages"></div>
            <h2><?php echo Yii::t('westnet', 'Connection Details') ?></h2>
            <div class="title">
                <p>
                    <?= $this->render('_contract-detail-buttons', [ 'model' => $model, 'connection' => $connection ])?>
                </p>
            </div>
            <?php
            // var_dump($connection->ip4_public);die();
            echo DetailView::widget([
                'model' => $connection,
                'attributes' => [
                    [
                        'label' => Yii::t('westnet', 'Server'),
                        'format' => 'html',
                        'value' => function($model){

                            $retHTML = ($model->server ? $model->server->name : "" );
                            if(isset($model->server->load_balancer_type)){ // is mikrotik server, then
                                if($model->server->load_balancer_type == 'Mikrotik'){
                                    return  Html::a('<span class="label label-success">Actualizar IP Principal en '.$retHTML.'</span>', 
                                    ['/westnet/connection/update-on-mikrotik', 'connection_id' => $model->connection_id], 
                                    ['class' => 'profile-link']);   
                                }
                            }
                            return $retHTML;
                        },
                    ],
                    [
                        'label' => Yii::t('westnet', 'Node'),
                        'value' => ($connection->node ? $connection->node->name : "" ),
                    ],
                    [
                        'label' => Yii::t('westnet', 'Access Point'),
                        'value' => ($connection->accessPoint ? $connection->accessPoint->name : "" ),
                    ],
                    [
                        'label' => Yii::t('westnet', 'ONU sn'),
                        'value' => ($connection->onu_sn),
                    ],
                    [
                        'label' => Yii::t('westnet', 'ip4_1'),
                        'value' => long2ip($connection->ip4_1),
                    ],
                    [
                        'label' => 'Ip4_1 Anterior',
                        'value' => function($model)
                        {
                            return long2ip($model->ip4_1_old);
                        },
                    ],
                    [
                        'label' => Yii::t('westnet', 'ip4_2'),
                        'format' => 'raw',
                        'value' => '<span id="bridge" data-toogle="tooltip" title="IP Secundaria">' . long2ip($connection->ip4_2) . '</span>',
                    ],
                    [
                        'label' => Yii::t('westnet', 'ip4_public'),
                        'value' => function($model)
                        {
                            return (!empty($model->ip4_public)) ?  long2ip($model->ip4_public) : $model->ip4_public;
                        },
                    ],
                    [
                        'label' => 'Estado de Conexión',
                        'value' => Yii::t('westnet', ucfirst($connection->status)),
                    ],
                    [
                        'label' => Yii::t('app', 'Status Account'),
                        'value' => Yii::t('app', ucfirst($connection->status_account) . ' Account'),
                    ],
                    [
                        'label' => Yii::t('westnet', 'Forced Activation Due Date'),
                        'format' => 'raw',
                        'value' => $connection->due_date,
                    ],
                    'mac_address'

                ],
            ]);
        }
        ?>

    <?php } ?>

    <h2><?php echo Yii::t('app', 'Contract Details') ?></h2>

    <?= GridView::widget([
        'dataProvider' => ContractDetailSearch::getdataProviderDetail($model->contract_id),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => Yii::t('app', 'Product Type'),
                'value' => function($model) {
                    if (!empty($model->product)) {
                        return Yii::t('app', ucfirst($model->product->type));
                    }
                }
            ],
            [
                'header' => Yii::t('app', 'Product'),
                'value' => function($model) {
                    return  $model->product->name;
                    
                }
            ],
            [
                'attribute' => 'count',
                'value' => function($model) {
                    return $model->count > 0 ? $model->count : '';
                }
            ],
            [
                'header' => Yii::t('app', 'Amount'),
                'value' => function($model) {
                    return Yii::$app->getFormatter()->asCurrency(
                                    $model->funding_plan_id ?
                                            $model->fundingPlan->getFinalAmount() :
                                            $model->product->getFinalPrice($model));
                }
            ],
            [
                'header' => Yii::t('app', 'Quantity Payments'),
                'value' => function($model){
                    if (empty($model->fundingPlan->qty_payments)) {
                        return 1;
                    }else{
                        return $model->fundingPlan->qty_payments;
                    }
                }
            ],
            [
                'header' => Yii::t('app', 'Total amount funded'),
                'value' => function($model) {
                    return Yii::$app->getFormatter()->asCurrency(($model->funding_plan_id ?
                                            $model->fundingPlan->getFinalTotalAmount() :
                                            $model->count * $model->product->getFinalPrice($model)));
                }
            ],
            'from_date',
            'to_date',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->status ? Yii::t('app', ucfirst($model->status)) : null;
                }
            ],
            [
                'attribute' => 'vendor_id',
                'value' => function($model){ return $model->vendor ? $model->vendor->fullName : null; }
            ],
        ],
    ]);
    ?>

    <label>Dirección del Contrato</label>
    <div id="map_canvas" style="width: 100%; height: 300px;">

    </div>

</div>

<!-- Modal -->
<?= $this->render('_modal-contract-connection', ['model' => $model, 'products' => $products, 'vendors' => $vendors])?>

<!-- Modal de company -->
<?= $this->render('_modal-contract-company', ['connection' => $connection, 'model' => $model])?>

<!-- Modal de Nodo -->
<?= $this->render('_modal-contract-node', [ 'connection' => $connection ])?>

<!-- Modal de Impresion ADS -->
<?= $this->render('_modal-contract-ads')?>

<!-- Modal de Proceso de baja -->
<?= $this->render('_modal-contract-low-process')?>

<!-- Modal comienzo de proceso de baja-->
<?= $this->render('_modal-contract-start-low-process')?>

<script>
    var ContractView = new function () {
        
        this.tentative_node= '<?php 
                             $tentativeNode= Node::findOne(['subnet' => $model->tentative_node]); 
                            echo (empty($tentativeNode) ? '0' : $tentativeNode->node_id ); ?>';
        this.init = function () {
            $(document).off('click', '#disable-connection')
                    .on('click', '#disable-connection', function () {
                        $(this).button('loading');
                        ContractView.disable();
                    });
            $(document).off('click', '#enable-connection')
                    .on('click', '#enable-connection', function () {
                        $(this).button('loading');
                        $("#force-connection").button('loading');
                        ContractView.enable();
                    });
            $(document).off('click', '#force-connection')
                    .on('click', '#force-connection', function () {
                        $(this).button('loading');
                        $("#enable-connection").button('loading');
                        ContractView.showForce();
                    });

            $(document).off('click', '#change-company')
                    .on('click', '#change-company', function () {
                        ContractView.showChangeCompany();
                    });

            $(document).off('click', '#change-node')
                    .on('click', '#change-node', function () {
                        ContractView.showChangeNode();
                    });

            $(document).off('click', '#btn-change-company').on('click', '#btn-change-company', function (e) {
                $(this).button('loading');
                ContractView.changeCompany()
            });

            $(document).off('click', '#btn-change-node').on('click', '#btn-change-node', function () {
                $(this).button('loading');
                ContractView.changeNode()
            });
            
            $(document).on('click', '#btn-definitive-low', function (e) {
                e.preventDefault();
                $(this).button('loading');
                ContractView.showLowProcess();
            });
            
            $(document).on('click', '#low-button', function (e) {
                e.preventDefault();
                $(this).button('loading');
                ContractView.lowProcess();
            });

            $(document).on('click', '#change-ip', function (e) {
                e.preventDefault();
                $(this).button('loading');
                ContractView.changeIp();
            });

            $(document).off('click', '#btn-active-new-items').on('click', '#btn-active-new-items', function () {
                $(this).button('loading');
                ContractView.activeNewItems()
            });
            $(document).on('click', '#print-button', function(e){
               e.preventDefault();
               $('#ads-modal').modal('hide');
               ContractView.printAds();
            });
            
            $(document).on('click', '#print-ads', function(e){

               e.preventDefault();
               if(ContractView.tentative_node === '0'){
                    $('#ads-modal').modal();
               }else{
                   ContractView.printAds(ContractView.tentative_node);
                }
            });

            $(document).off('click', '#btn-low-process').on('click', '#btn-low-process', function(evt){
                evt.preventDefault();
                $('#start-low-process-modal').modal();
            });

            $(document).off('click', '#start-low-button').on('click', '#start-low-button', function(evt){
                evt.preventDefault();
                var category_id = $('#start-low-process-modal select').val();
                var date = $('#start-low-process-modal #date_low').val();
                var credit = 0;

                if($('#credit_note').is(':checked')) {
                    credit = 1;
                }
                $('#start-low-process-modal').modal('hide');
                if(category_id) {
                    $.ajax({
                        url: '<?php echo Url::to(['/sale/contract/contract/low-process-contract', 'contract_id'=>$model->contract_id]) ?>',
                        data: {
                            category_id: category_id,
                            date: date,
                            credit: credit
                        },
                        method: 'POST',
                    }).done(function(data){
                        if (data.status == 'success') {
                            window.location.reload();
                        } else {
                            $('#messages').html('<div class="alert alert-danger">'+data.message +'</div>');
                        }
                    });
                }
            });

            $('#connection-modal').on('hidden.bs.modal',function(e){
                
                e.preventDefault();
                $('#connection-modal').modal('hide');
                $('#force-connection').button('reset');
                $('#enable-connection').button('reset');
                $('#message-con').empty();
            });

            $('#create_product').change(function () {
                if (!$('#create_product').is(':checked')) {

                    $('#extend_product_id').prop('disabled', 'disabled');
                    $('#vendor_id').prop('disabled', 'disabled');
                }else{

                    $('#extend_product_id').removeAttr('disabled');
                    $('#vendor_id').removeAttr('disabled');

                }
            })
            $('#node-modal').removeAttr('tabindex');
            ContractView.map();
            $('[data-toogle="tooltip"]').tooltip();
            ContractView.getNodes();            

        }

        this.disable = function () {
            if (confirm('<?= Yii::t('westnet', 'Are you sure you want to disable this connection?') ?>')) {
                ContractView.execute('<?= Url::to(['/westnet/connection/disable', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>')
            } else {
                $('#disable-connection').button('reset');
            }
        }

        this.enable = function () {
            if (confirm('<?= Yii::t('westnet', 'Are you sure you want to activate this connection?') ?>')) {
                ContractView.execute('<?= Url::to(['/westnet/connection/enable', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>')
            } else {
                $('#enable-connection').button('reset');
            }

        }

        this.showForce = function () {
            if (confirm('<?php echo Yii::t('westnet', 'Are you sure you want to force the activation of this connection?') ?>')) {
                $('#connection-modal').modal('show')
            } else {
                $('#force-connection').button('reset');
                $('#enable-connection').button('reset');
            }
        }

        this.force = function () {
            if ($('#due_date').val() !=='' && $('#reason').val() !== '' ) {
                $('#connection-modal').modal('hide')
                if ($('#due_date').kvDatepicker('getDate') != null) {
                    var vDate = $('#due_date').kvDatepicker('getDate');
                    ContractView.execute('<?= Url::to(['/westnet/connection/force', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>', {
                    due_date: $('#due_date').val(), reason: $('#reason').val(), create_product: $('#create_product').is(':checked'),
                        product_id: $('#extend_product_id').val(), vendor_id: $('#vendor_id').val()
                    });
                }
            }else{
                $('#message-con').html('<div class="alert alert-danger">Por favor, complete fecha de vencimiento y motivo.</div>');
            }
        }

        this.execute = function (url, data, button) {
            $.ajax({
                url: url,
                data: data,
                method: 'POST',
                success: function (data) {
                    if (data.status == 'success') {
                        window.location.reload();
                        return true;
                    } else {
                        if (button) {
                            $(button).button('reset');
                        }
                        $('#force-connection').button('reset');
                        $('#disable-connection').button('reset');
                        $('#enable-connection').button('reset');
                        if (data.message) {
                            $('#messages').html('<div class="alert alert-danger">'+data.message +'</div>');
                        } else {
                            alert('Error');
                        }
                        return false;
                    }
                }
            });
        }

        this.showChangeNode = function () {
            $('#node-modal').modal('show');
        }
        
        this.showLowProcess = function () {
            $('#low-process-modal').modal('show');
        }

        this.changeCompany = function () {
            var id = $('#form-company #connection-company_id').val();
            if (id) {
                ContractView.execute('<?= \yii\helpers\Url::to(['/sale/contract/contract/change-company', 'connection_id' => ($connection ? $connection->connection_id : '' )]) ?>&company_id=' + id, [], '#btn-change-company');
            }
        }

        this.changeNode = function () {
            var id = $('#form-node #node_id').val();
            var ap_id = $('#form-node #ap_id').val();
            if (id) {
                ContractView.execute('<?= \yii\helpers\Url::to(['/sale/contract/contract/change-node', 'connection_id' => ($connection ? $connection->connection_id : '')]) ?>&node_id=' + id + '&ap_id=' + ap_id, [], '#btn-change-node');
            }
        }

        this.activeNewItems = function () {
            ContractView.execute('<?= \yii\helpers\Url::to(['/sale/contract/contract/active-new-items', 'contract_id' => $model->contract_id]) ?>', [], '#btn-active-new-items');
        }

        this.map = function () {
            var lat = {<?php
                if ($model->address) {
                    $lt = explode(",", ($model->address->geocode == NULL ? "-32.8892793,-68.8438426" : $model->address->geocode));
                    echo "lat:" . $lt[0] . ", lng:" . $lt[1];
                }
                ?>};
            var map = new google.maps.Map(document.getElementById('map_canvas'), {
                center: lat,
                scrollwheel: false,
                zoom: 17,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            marker = new google.maps.Marker({
                position: lat,
                title: 'Direccion',
                map: map,
                draggable: false,
                scrollwheel: false
            });

        };
        
        this.getNodes= function(){
            $('#ads-modal #node_id option').remove();
            $.ajax({
                url : "<?= \yii\helpers\Url::to(['/westnet/node/all-nodes'])?>",
                method: "post",
                dataType: "json",
                success: function(data){
                    $.each(data, function(i,n){
                        $('#ads-modal #node_id').append('<option value="'+n.node_id+'">'+n.name+'</option>');
                    });
                    
                    
                }
            });
        }

        this.printAds= function(node){
            if(node === '0' || typeof  node === 'undefined'){
                window.open("<?= Url::to(['/westnet/ads/print'])?>&id=<?=$model->contract_id?>&node_id="+ $('#node_id').val());
                location.reload();
            }else{
                window.open("<?= Url::to(['/westnet/ads/print'])?>&id=<?=$model->contract_id?>&node_id="+ node);
                location.reload();
            }

        }
        
        this.lowProcess= function(){
            $.ajax({
                url : '<?= Url::to(['/sale/contract/contract/cancel-contract'])?>',
                data: {id: <?= $model->contract_id?>, mac_address: $('#mac-address').val()},
                dataType: 'json',
                success: function(data){
                    location.href= '<?= Url::to(['/sale/contract/contract/view', 'id' => $model->contract_id])?>';
                }
            });
        }

        this.changeIp = function () {
            if (confirm('<?= Yii::t('westnet', 'Are you sure you want to change the ip of this connection?') ?>')) {
                ContractView.execute('<?= Url::to(['/sale/contract/contract/change-ip', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>')
            } else {
                $('#change-ip').button('reset');
            }
        };

    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>

<?php $this->registerJs("ContractView.init();"); ?>
