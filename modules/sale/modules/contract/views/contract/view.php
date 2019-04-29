<?php

use app\modules\config\models\Config;
use app\modules\sale\models\Company;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Node;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\components\helpers\UserA;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\Contract */



$this->title = Yii::t('app', 'Contract of') . ": " . $model->customer->fullName . " - " . $model->customer->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['/sale/customer/index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->name, 'url' => ['/sale/customer/view', 'id' => $model->customer_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Contract Number') . ": " . $model->contract_id;
?>
<div class="contract-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
            if ($model->status != Contract::STATUS_CANCELED && $model->canUpdate()) {
                echo UserA::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->contract_id], ['class' => 'btn btn-primary']);
            }
            ?>
            <?php
            if ($model->status == Contract::STATUS_LOW_PROCESS) {
                echo UserA::a(Yii::t('app', 'Reactive Contract'), ['active-contract-again', 'contract_id' => $model->contract_id], ['class' => 'btn btn-success']);
            }
            ?>
            <?php
            if (Yii::$app->getModule('westnet') && $model->status === Contract::STATUS_DRAFT && $model->canPrintAds()) {
                echo UserA::a(Yii::t('app', 'Print') . ' ADS', '#', ['class' => 'btn btn-success', 'id' =>'print-ads']);
            }

            if ($model->status == Contract::STATUS_DRAFT) {
                echo UserA::a(Yii::t('app', 'Active Contract'), ['active-contract', 'id' => $model->contract_id], ['class' => 'btn btn-success']);
            } elseif ($model->status == Contract::STATUS_ACTIVE && $model->getContractDetails()->filterWhere(['status' => Contract::STATUS_DRAFT])->count() > 0) {
                echo UserA::a(Yii::t('app', 'Active new items of Contract'), null, [
                    'class' => 'btn btn-success',
                    'data-loading-text' => Yii::t('app', 'Processing'),
                    'id' => 'btn-active-new-items'
                ]);
            }

            if ($model->deletable)
                echo UserA::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->contract_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            ?>

            <?= UserA::a(Yii::t('app', 'History'), ['history', 'id' => $model->contract_id], ['class' => 'btn btn-default']) ?>
            
            <?php
            if ($model->status == Contract::STATUS_ACTIVE) {
                if(!$model->low_date) {
                    echo UserA::a(Yii::t('app', 'Begin Low Process'), null, ['class' => 'btn btn-danger', 'id' => 'btn-low-process', 'data-id'=>$model->contract_id]);
                }
            }
            if ($model->status == Contract::STATUS_LOW_PROCESS) {
                echo UserA::a(Yii::t('app', 'Definitive Low'), ['cancel-contract', 'id' => $model->contract_id], ['class' => 'btn btn-danger', 'id' => 'btn-definitive-low' ]);
            }
            if ($model->status == Contract::STATUS_DRAFT) {
                echo UserA::a(Yii::t('app', 'No want the Service'), ['rejected-service', 'id' => $model->contract_id, 'type' => Contract::STATUS_NO_WANT], ['class' => 'btn btn-danger']);
            }
            if ($model->status == Contract::STATUS_DRAFT) {
                echo UserA::a(Yii::t('app', 'Negative Survey'), ['rejected-service', 'id' => $model->contract_id, 'type' => Contract::STATUS_NEGATIVE_SURVEY], ['class' => 'btn btn-danger' ]);
            }
            if($model->status == Contract::STATUS_NEGATIVE_SURVEY){
                echo UserA::a(Yii::t('app', 'Revert negative survey'), ['revert-negative-survey', 'contract_id' => $model->contract_id], ['class' => 'btn btn-danger' ]);
            }
            ?>
            
        </p>
    </div>
    <?php
    $columns = [
        [
            'label' => Yii::t('app', 'Customer'),
            'value' => $model->customer->fullName
        ],
        [
            'label' => Yii::t('app', 'Company'),
            'value' => function($model){
                if (!$model->customer->company) {
                    return '';
                }
                $result = $model->customer->company->name;
                if ($model->customer->parentCompany) {
                    $result .= " ( " . $model->customer->parentCompany->name ." )";
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
            'value' => (isset($model->address) ? $model->address->fullAddress : $model->customer->address ),
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
        'value' => (empty($model->tentative_node) ? '' : Node::findOne(['subnet' => $model->tentative_node])->name)
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
        $connection = \app\modules\westnet\models\Connection::findOne(['contract_id' => $model->contract_id]);
        if ($connection) {
            ?>
            <div id="messages"></div>
            <h2><?php echo Yii::t('westnet', 'Connection Details') ?></h2>
            <div class="title">
                <p>
                    <?php
                    if ($model->status == Contract::STATUS_ACTIVE) {
                        echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update-connection', 'id' => $model->contract_id], [
                            'class' => 'btn btn-primary',
                            'id' => 'update-connection',
                        ]);
                        
                        echo Html::a(Yii::t('westnet', 'Connection Forced Historials'), ['/westnet/connection-forced-historial/index', 'connection_id'=>$connection->connection_id], [
                            'class' => 'btn btn-info',
                            
                        ]);
                        echo Html::a(Yii::t('westnet', 'Change Node'), null, [
                            'class' => 'btn btn-warning',
                            'id' => 'change-node',
                        ]);

                        echo Html::a(Yii::t('westnet', 'Change IP'), null, [
                            'class' => 'btn btn-warning',
                            'id' => 'change-ip',
                        ]);

                        if ($connection->status == Connection::STATUS_ENABLED) {
                            echo Html::a(Yii::t('westnet', 'Disable'), null, [
                                'class' => 'btn btn-danger',
                                'id' => 'disable-connection',
                                'data-loading-text' => Yii::t('westnet', 'Disabling') . "..."
                            ]);
                        }

                        if ($connection->status == Connection::STATUS_DISABLED) {
                            echo Html::a(Yii::t('westnet', 'Activate'), null, [
                                'class' => 'btn btn-danger',
                                'id' => 'enable-connection',
                                'data-loading-text' => Yii::t('westnet', 'Enabling') . "..."
                            ]);
                        }


                        echo Html::a(Yii::t('westnet', 'Force Activation'), null, [
                            'class' => 'btn btn-danger',
                            'id' => 'force-connection',
                            'data-loading-text' => Yii::t('westnet', 'Enabling') . "..."
                        ]);
                    }
                    ?>
                </p>
            </div>
            <?php

            echo DetailView::widget([
                'model' => $connection,
                'attributes' => [
                    [
                        'label' => Yii::t('westnet', 'Server'),
                        'value' => ($connection->server ? $connection->server->name : "" ),
                    ],
                    [
                        'label' => Yii::t('westnet', 'Node'),
                        'value' => ($connection->node ? $connection->node->name : "" ),
                    ],
                    [
                        'label' => Yii::t('westnet', 'ip4_1'),
                        'value' => long2ip($connection->ip4_1),
                    ],
                    [
                        'label' => Yii::t('westnet', 'ip4_2'),
                        'format' => 'raw',
                        'value' => '<span id="bridge" data-toogle="tooltip" title="IP Secundaria">' . long2ip($connection->ip4_2) . '</span>',
                    ],
                    [
                        'label' => Yii::t('westnet', 'ip4_public'),
                        'value' => long2ip($connection->ip4_public),
                    ],
                    [
                        'label' => Yii::t('app', 'Status'),
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
                    ]
                ],
            ]);
        }
        ?>

    <?php } ?>

    <h2><?php echo Yii::t('app', 'Contract Details') ?></h2>

    <?=
    GridView::widget([
        'dataProvider' => \app\modules\sale\modules\contract\models\search\ContractDetailSearch::getdataProviderDetail($model->contract_id),
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
            ]
        ],
    ]);
    ?>

    <label>Dirección del Contrato</label>
    <div id="map_canvas" style="width: 100%; height: 300px;">

    </div>

</div>
<!-- Modal -->
<div class="modal fade" id="connection-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Forced Activation') ?></h4>
            </div>
            <div class="modal-body">
                <div id="message-con"></div>
                <div class="form-group">
                    <label for="due_date" class="control-label"><?php echo Yii::t('westnet', 'Forced Activation Due Date') ?></label>
                    <?php
                    echo DatePicker::widget([
                        'name' => 'due_date',
                        'type' => 1,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy',
                        ],
                        'options' => [
                            'class' => 'form-control filter dates',
                            'placeholder' => Yii::t('app', 'Date'),
                            'id' => 'due_date'
                        ]
                    ]);
                    ?>
                </div>
                <div class="form-group">
                    <label for="due_date" class="control-label"><?php echo Yii::t('app', 'Reason') ?></label>
                    <textarea cols="35" rows="5" id="reason" class="form-control"></textarea>
                </div>
                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="force-conn-bton-cancel"><?php echo Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="ContractView.force()" data-loading-text="<?php echo Yii::t('app', 'Processing') ?>"><?php echo Yii::t('app', 'Update') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de company -->
<div class="modal fade" id="company-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Change Company') ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $form = ActiveForm::begin(['id' => 'form-company']);
                if ($connection) {
                    $connection->company_id = ($connection->company_id ? $connection->company_id : ($connection->node ? $connection->node->company_id : $model->customer->company_id) );

                    echo $form->field($connection, 'company_id')
                            ->label(Yii::t('app', 'Company'))
                            ->dropDownList(ArrayHelper::map(Company::find()->all(), 'company_id', 'name'), [
                                'prompt' => Yii::t('app', 'Select')
                    ]);
                }
                ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <?php
                echo Html::a(Yii::t('app', 'Update'), null, [
                    'class' => 'btn btn-primary',
                    'id' => 'btn-change-company',
                    'data-loading-text' => Yii::t('app', 'Processing') . "..."
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Nodo -->
<div class="modal fade" id="node-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('westnet', 'Change Node') ?></h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin(['id' => 'form-node']); ?>
                <div class="form-group field-contract-node-id required">

                    <?php
                    Html::label(Yii::t('westnet', 'Node'));
                    if ($connection) {
                        $query = Node::find();
                        $query->select(['node.node_id', 'concat(node.name, \' - \', s.name) as name'])
                                ->leftJoin('server s', 'node.server_id = s.server_id');

                        echo $form->field($connection, 'node_id')->widget(Select2::className(), [
                            'data' => yii\helpers\ArrayHelper::map($query->all(), 'node_id', 'name'),
                            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
                            'pluginOptions' => [
                                'allowClear' => true
                            ]
                                ]
                        );
                    }
                    ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <?php
                echo Html::a(Yii::t('app', 'Update'), null, [
                    'class' => 'btn btn-primary',
                    'id' => 'btn-change-node',
                    'data-loading-text' => Yii::t('app', 'Processing') . "..."
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Impresion ADS -->
<div class="modal fade" id="ads-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
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
                <button type="button" class="btn btn-primary" id="print-button"><?php echo Yii::t('app', 'Print') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Proceso de baja -->
<div class="modal fade" id="low-process-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:25%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('app', 'Low Process') ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <label><?= Yii::t('app', 'MAC Address Device') ?></label>
                    <input type="text" id="mac-address" class="form-control"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-danger" id="low-button"><?php echo Yii::t('app', 'Definitive Low') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="start-low-process-modal" tabindex="-1" role="dialog" aria-labelledby="start-low-process-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="start-low-process-label"><?php echo Yii::t('app', 'Low Process') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label><?= Yii::t('westnet', 'Reason of low') ?></label>
                        </div>
                        <div class="col-md-12">
                            <?php
                            $categories = \app\modules\ticket\models\Category::getForSelectChilds(Config::getValue('mesa_category_low_reason'));
                            echo Select2::widget([
                                'name' => 'category_id',
                                'data' => yii\helpers\ArrayHelper::map($categories, 'category_id', 'name'),
                                'options' => ['placeholder' => Yii::t("westnet", "Select an reason..."), 'encode' => false],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label><?= Yii::t('westnet', 'Date of low') ?></label>
                        </div>
                        <div class="col-md-12">
                            <?php
                            echo DatePicker::widget([
                                'type' => 1,
                                'language' => Yii::$app->language,
                                'name' => 'date_low',
                                'value' => (new \DateTime('now'))->format('d-m-Y'),
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-mm-yyyy',
                                ],
                                'options'=>[
                                    'class'=>'form-control filter dates',
                                    'placeholder'=>Yii::t('app','Date'),
                                    'id' => 'date_low',
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel') ?></button>
                <button type="button" class="btn btn-primary" id="start-low-button"><?php echo Yii::t('app', 'Low') ?></button>
            </div>
        </div>
    </div>
</div>

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
                $('#start-low-process-modal').modal('hide');
                if(category_id) {
                    $.ajax({
                        url: '<?php echo Url::to(['/sale/contract/contract/low-process-contract', 'contract_id'=>$model->contract_id]) ?>',
                        data: {
                            category_id: category_id,
                            date: date
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
            })
            $('#node-modal').removeAttr('tabindex');
            ContractView.map();
            $('[data-toogle="tooltip"]').tooltip();
            ContractView.getNodes();            

        }

        this.disable = function () {
            if (confirm('<?php echo Yii::t('westnet', 'Are you sure you want to disable this connection?') ?>')) {
                ContractView.execute('<?php echo \yii\helpers\Url::to(['/westnet/connection/disable', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>')
            } else {
                $('#disable-connection').button('reset');
            }
        }

        this.enable = function () {
            if (confirm('<?php echo Yii::t('westnet', 'Are you sure you want to activate this connection?') ?>')) {
                ContractView.execute('<?php echo \yii\helpers\Url::to(['/westnet/connection/enable', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>')
            } else {
                $('#enable-connection').button('reset');
            }

        }

        this.showForce = function () {
            if (confirm('<?php echo Yii::t('westnet', 'Are you sure you want to force the activation of this connection?') ?>')) {
                $('#due_date').val('');
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
                    ContractView.execute('<?php echo \yii\helpers\Url::to(['/westnet/connection/force', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>', {
                    '   due_date': vDate.getFullYear() + "-" + (vDate.getMonth() + 1) + "-" + vDate.getDate(), reason: $('#reason').val()
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
                ContractView.execute('<?php echo \yii\helpers\Url::to(['/sale/contract/contract/change-company', 'connection_id' => ($connection ? $connection->connection_id : '' )]) ?>&company_id=' + id, [], '#btn-change-company');
            }
        }

        this.changeNode = function () {
            var id = $('#form-node #connection-node_id').val();
            if (id) {
                ContractView.execute('<?php echo \yii\helpers\Url::to(['/sale/contract/contract/change-node', 'connection_id' => ($connection ? $connection->connection_id : '')]) ?>&node_id=' + id, [], '#btn-change-node');
            }
        }

        this.activeNewItems = function () {
            ContractView.execute('<?php echo \yii\helpers\Url::to(['/sale/contract/contract/active-new-items', 'contract_id' => $model->contract_id]) ?>', [], '#btn-active-new-items');
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
                window.open("<?= \yii\helpers\Url::to(['/westnet/ads/print'])?>&id=<?=$model->contract_id?>&node_id="+ $('#node_id').val());
                location.reload();
            }else{
                window.open("<?= \yii\helpers\Url::to(['/westnet/ads/print'])?>&id=<?=$model->contract_id?>&node_id="+ node);
                location.reload();
            }

        }
        
        this.lowProcess= function(){
            $.ajax({
                url : '<?= \yii\helpers\Url::to(['/sale/contract/contract/cancel-contract'])?>',
                data: {id: <?= $model->contract_id?>, mac_address: $('#mac-address').val()},
                dataType: 'json',
                success: function(data){
                    location.href= '<?= \yii\helpers\Url::to(['/sale/contract/contract/view', 'id' => $model->contract_id])?>';
                }
            });
        }

        this.changeIp = function () {
            if (confirm('<?php echo Yii::t('westnet', 'Are you sure you want to change the ip of this connection?') ?>')) {
                ContractView.execute('<?php echo \yii\helpers\Url::to(['/sale/contract/contract/change-ip', 'id' => ($connection ? $connection->connection_id : 0 )]) ?>')
            } else {
                $('#change-ip').button('reset');
            }
        };

    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>

<?php $this->registerJs("ContractView.init();"); ?>
