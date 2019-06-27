<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\ticket\TicketModule;
use app\modules\config\models\Config;
use yii\bootstrap\ButtonDropdown;
use \yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use app\modules\ticket\models\Observation;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Ticket */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['open-tickets']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-view">

    <div class="title">
        <h1>
        <span style="color: <?= $model->color->color; ?>;">
            [<?= $model->number?>] <?= Html::encode($this->title); ?>
        </span>
            <small>[<?= $model->status->name; ?>]</small>
        </h1>

        <!-- Ticket options -->
        <div class="row">
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->ticket_id], [
                    'class' => 'btn btn-primary btn-sm',
            ])?>
            <?= Html::a('<span class="glyphicon glyphicon-zoom-in"></span> ' . TicketModule::t('app', 'Observations'), ['observation', 'id' => $model->ticket_id], ['class' => 'btn btn-info btn-sm']) ?>

            <?php if ($model->statusIsActive()) : ?>
                <?= Html::a('<span class="glyphicon glyphicon-ok"></span> ' . TicketModule::t('app', 'Close ticket'), ['close', 'id' => $model->ticket_id], ['class' => 'btn btn-success btn-sm']) ?>
            <?php else : ?>
                <?= Html::a('<span class="glyphicon glyphicon-refresh"></span> ' . TicketModule::t('app', 'Reopen ticket'), ['reopen', 'id' => $model->ticket_id], ['class' => 'btn btn-warning btn-sm']) ?>
            <?php endif; ?>
            <?php if ($model->deletable) {
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->ticket_id], [
                    'class' => 'btn btn-danger btn-sm',
                    'data' => [
                        'confirm' => TicketModule::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }?>

            <?php
                $credit_bill_cat_id = Config::getValue('credit-bill-category-id');
                $bill_cat_id= Config::getValue('bill-category-id');

                if($model->category_id === (int)$credit_bill_cat_id || $model->category_id === (int)$bill_cat_id){
                    $billTypes2Create = $model->customer->company->billTypes;
                    $billItems = [];

                    foreach ($billTypes2Create as $item) {

                        $billItems[] = ['label' => $item->name, 'url' => ['/sale/bill/create', 'type' => $item->bill_type_id, 'customer_id'=> $model->customer_id, 'company_id' => $model->customer->company_id ]];
                    }

                    echo ButtonDropdown::widget([
                        'label' => Yii::t('app','Create Bill'),
                        'dropdown' => [
                            'items' => $billItems,
                            'encodeLabels'=>false,
                            'options' => ['class' => 'dropdown-menu dropdown-menu-left']
                        ],
                        'options'=>[
                            'class'=>'btn btn-warning btn-sm pull-right',
                            'id' => 'nc-button'
                        ]
                    ]);
                }
            ?>

            <?= Html::a('<span class="glyphicon glyphicon-time"></span> ' . TicketModule::t('app', 'View history'), ['history', 'id' => $model->ticket_id], ['class' => 'btn btn-default  btn-sm pull-right']) ?>
        </div>
    </div>

    <?php
    $attributes = [
        'ticket_id',
        [
            'attribute' => 'customer_id',
            'value' => function ($model) {
                return ($model->customer_id ?
                    Html::a($model->customer
                        ->fullName, ['/sale/customer/view', 'id' => $model->customer_id]) : '');
            },
            'format' => 'raw'
        ],
        [
            'label' => $model->getAttributeLabel('category_id'),
            'value' => $model->category ? $model->category->name : ''
        ],
        'start_date',
        'finish_date',
        'content:ntext',
    ];

    if ($model->statusIsActive() && !empty($model->getLastHistoryOpen())) {
        $attributes[] = [
            'label' => TicketModule::t('app', 'Opened by'),
            'value' => $model->getLastHistoryOpen()->user->username .' - El '.$model->getLastHistoryOpen()->date .' a las '. $model->getLastHistoryOpen()->time,
        ];
    } elseif (!empty($model->getLastHistoryClosed())) {
        $attributes[] = [
            'label' => TicketModule::t('app', 'Closed by'),
            'value' => $model->getLastHistoryOpen()->user->username .' - El '. $model->getLastHistoryClosed()->date .' a las '.$model->getLastHistoryClosed()->time,
        ];
    }?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]) ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <h3 class="panel-title">
                    <?= TicketModule::t('app', 'Observations') ?>
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create observation'), ['#'], [
                        'class' => 'pull-right',
                        'id' => 'create-observation',
                        'data-toggle' => 'modal',
                        'data-target' => '#observation-modal',
                    ])?>
                </h3>
            </div>

        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => new ActiveDataProvider(['query' => $model->getObservations()]),
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
    </div>
</div>

<?php Modal::begin([
    'header' => '<h2>'.Yii::t('app', 'Create observation').'</h2>',
    'options' => [
        'id' => 'observation-modal'
    ],
]);

echo $this->render('/observation/_new-observation', [
        'ticket_id' => $model->ticket_id,
        'model' => new Observation(),
]);

Modal::end();?>

<script>
    var TicketView = new function(){
        this.init= function(){
            $.each($('#nc-button').parent().find('a'), function(i, a){
                $(a).attr('target', '_blank');
            })

            $('#create-observation').on('click', function (evt) {
                evt.preventDefault();
            })

            $('#observation-submit-btn').on('click', function (evt) {
                evt.preventDefault();
                TicketView.createObservation();
            })
        }
        
        this.createObservation = function () {
            $.ajax({
                url: '<?= Url::to(['observation/create'])?>',
                method: 'POST',
                data: $('#observation-form').serializeArray(),
                dataType: 'json',
                success: function(data){
                    console.log(data);
                    if(data.status == 'success') {
                        location.reload();
                    }
                }
            });
        }
    }
</script>
<?php $this->registerJs('TicketView.init()')?>
