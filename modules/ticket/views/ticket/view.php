<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Ticket */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['open-tickets']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-view">

    <h1>
        <span style="color: <?= $model->color->color; ?>;">
            [<?= $model->number; ?>]
            <?= Html::encode($this->title); ?> 
        </span>
        <small>[<?= $model->status->name; ?>]</small>
    </h1>

    <?php if ($model->statusIsActive() && !empty($model->getLastHistoryOpen())) : ?>
        <p>
            <?= \app\modules\ticket\TicketModule::t('app', 'Opened by'); ?> <?= $model->getLastHistoryOpen()->user->username; ?>
            , <?= $model->getLastHistoryOpen()->date; ?> <?= $model->getLastHistoryOpen()->time; ?>

        </p>
    <?php elseif (!empty($model->getLastHistoryClosed())) : ?>
        <p>
            <?= \app\modules\ticket\TicketModule::t('app', 'Closed by'); ?> <?= $model->getLastHistoryClosed()->user->username; ?>
            , <?= $model->getLastHistoryClosed()->date; ?> <?= $model->getLastHistoryClosed()->time; ?>
        </p>
    <?php endif; ?>

    <!-- Ticket options -->
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->ticket_id], ['class' => 'btn btn-primary']) ?>
        <?php
        if ($model->deletable)
            echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->ticket_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => \app\modules\ticket\TicketModule::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
        <?= Html::a('<span class="glyphicon glyphicon-zoom-in"></span> ' . \app\modules\ticket\TicketModule::t('app', 'Observations'), ['observation', 'id' => $model->ticket_id], ['class' => 'btn btn-info']) ?>
        <?php if ($model->statusIsActive()) : ?>
            <?= Html::a('<span class="glyphicon glyphicon-ok"></span> ' . \app\modules\ticket\TicketModule::t('app', 'Close ticket'), ['close', 'id' => $model->ticket_id], ['class' => 'btn btn-success']) ?>        
        <?php else : ?>
            <?= Html::a('<span class="glyphicon glyphicon-refresh"></span> ' . \app\modules\ticket\TicketModule::t('app', 'Reopen ticket'), ['reopen', 'id' => $model->ticket_id], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
        
        <?php 
            $credit_bill_cat_id= app\modules\config\models\Config::getValue('credit-bill-category-id');
            $bill_cat_id= app\modules\config\models\Config::getValue('bill-category-id');
            
            if($model->category_id === (int)$credit_bill_cat_id || $model->category_id === (int)$bill_cat_id){
                $billTypes2Create = $model->customer->company->billTypes;
                $billItems = [];
                
                foreach ($billTypes2Create as $item) {
                    
                    $billItems[] = ['label' => $item->name, 'url' => ['/sale/bill/create', 'type' => $item->bill_type_id, 'customer_id'=> $model->customer_id, 'company_id' => $model->customer->company_id ]];
                }
                
                echo yii\bootstrap\ButtonDropdown::widget([
                    'label' => Yii::t('app','Create Bill'),
                    'dropdown' => [
                        'items' => $billItems,
                        'encodeLabels'=>false,
                        'options' => ['class' => 'dropdown-menu dropdown-menu-left']
                    ],
                    'options'=>[
                        'class'=>'btn btn-warning',
                        'id' => 'nc-button'
                    ]
                ]);
            }
        ?>
            

        <?= Html::a('<span class="glyphicon glyphicon-time"></span> ' . \app\modules\ticket\TicketModule::t('app', 'View history'), ['history', 'id' => $model->ticket_id], ['class' => 'btn btn-default pull-right']) ?>
    </p>
    <!-- end Ticket options -->

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ticket_id',
            [
                'label' => $model->getAttributeLabel('customer_id'),
                'value' => $model->customer ? $this->render("../customer/customer_info", ['model' => $model->customer]) : '',
                'format' => 'html'
            ],
            [
                'label' => $model->getAttributeLabel('category_id'),
                'value' => $model->category ? $model->category->name : ''
            ],
            [
                'label' => $model->getAttributeLabel('status_id'),
                'value' => $model->status ? $model->status->name : ''
            ],
            'start_date',
            'finish_date',
            'title',
            'content:ntext',
        ],
    ])
    ?>
    
    
    <h2><?= \app\modules\ticket\TicketModule::t('app', 'Observations')?></h2>
    
    <?= yii\grid\GridView::widget([
        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getObservations()]),
        'columns' => [
            'title',
            'description',
            'date',
            'time',
            [
                'label' =>  Yii::t('app', 'User'),
                'attribute' => 'user.username'
            ]
        ]
    ]) ?>

</div>

<script>

    var TicketView = new function(){
        this.init= function(){
            $.each($('#nc-button').parent().find('a'), function(i, a){
                $(a).attr('target', '_blank');
            })
        }
    }

</script>
<?php $this->registerJs('TicketView.init()')?>
