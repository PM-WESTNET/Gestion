<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\Cashier;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = EcopagosModule::t('app', 'Payouts');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payout-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . EcopagosModule::t('app', 'Create Payout'), ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?php if ($dailyClosure !== NULL) : ?>
        <ul class="nav nav-pills">
            <li role="presentation"><a><h4>Cantidad de Pagos registrados desde el último cierre: <?= $dailyClosure->payment_count ?></h4></a></li>
            <li role="presentation"><a><h4>Numero del último pago registrado: <?= $dailyClosure->last_payout_number ?></h4></a></li>
            <li role="presentation"><a><h4>Monto total cobrado : $<?php setlocale(LC_MONETARY, 'es-AR');
    echo money_format('%.2n', $dailyClosure->total); ?></h4></a></li>
            <li role="presentation"><a><h4>Cobros Cancelados:  <?= $reversed ?></h4></a>
        </ul>
    <?php endif; ?>
    <?php
    \yii\widgets\Pjax::begin();

    $columns = [
        'payout_id',
        [
            'attribute' => 'customer_number',
            'header' => EcopagosModule::t('app', 'Customer number'),
            'value' => 'customer_number',
            'contentOptions' => function($model) { return (!$model->isReversable() ? ['class' => 'disable-status'] :[]);},
        ],
        [
            'attribute' => 'customer',
            'header' => EcopagosModule::t('app', 'Customer'),
            'value' => function($model) {
                if (!empty($model->customer))
                    return $model->customer->name . ' ' . $model->customer->lastname;
            },
            'contentOptions' => function($model) { return (!$model->isReversable() ? ['class' => 'disable-status'] :[]);},
        ],
        [
            'attribute' => 'cashier_id',
            'format' => 'raw',
            'filter' => ArrayHelper::map(Cashier::fetchCashiersFromEcopago(UserHelper::getEcopago()->ecopago_id), 'cashier_id', 'name'),
            'header' => EcopagosModule::t('app', 'Cashier'),
            'value' => function($model) {
                if (!empty($model->cashier))
                    return $model->cashier->name;
            },
            'contentOptions' => function($model) { return (!$model->isReversable() ? ['class' => 'disable-status'] :[]);},
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \app\modules\westnet\ecopagos\models\Payout::staticFetchStatuses(),
            'header' => EcopagosModule::t('app', 'Status'),
            'value' => function($model) {
                if (!empty($model->status))
                    return $model->fetchStatuses()[$model->status];
            },
            'contentOptions' => function($model) { return (!$model->isReversable() ? ['class' => 'disable-status'] :[]);},
        ],
        'date',
        'time',
        [
            'attribute' => 'daily_closure_id',
            'header' => EcopagosModule::t('app', 'Daily closure'),
            'value' => function($model) {
                if (!empty($model->dailyClosure))
                    return $model->dailyClosure->daily_closure_id;
            },
        ],
        [
            'attribute' => 'batch_closure_id',
            'header' => EcopagosModule::t('app', 'Batch closure'),
            'value' => function($model) {
                if (!empty($model->batchClosure))
                    return $model->batchClosure->batch_closure_id;
            },
            'pageSummary' => 'Total',
        ],
        [
            'attribute' => 'amount',
            'pageSummary' => true,
            'format' => ['currency']
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'buttons' => [
                'cancel' => function($url, $model, $key) {
                    if ($model->isReversable()) {

                        return Html::a('<span class="glyphicon glyphicon-remove"></span> ', '#', [
                                    'class' => 'cancelButton',
                                    'data-id' => $model->payout_id,
                        ]);
                    }
                },
                
            ],
            'template' => '{view} {cancel}',
        ]
            ];

            echo GridView::widget([
                'showPageSummary' => false,
                'resizableColumns' => true,
                'dataProvider' => $dataProvider,
                'columns' => $columns,
                'id' => 'grid',
                'filterModel' => $searchModel,
                'filterSelector' => '.filter',
                'responsive' => true,
                'hover' => true,
                'resizableColumns' => true,
                'rowOptions' => function($model) {
                    
                },
            ]);

            \yii\widgets\Pjax::end();
            ?>

        </div>


        <!-- Reverse Modal -->
        <div class="modal fade" id="reverse-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?= EcopagosModule::t('app', 'Confirm payout reverse'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?= EcopagosModule::t('app', 'Are you sure you want to reverse this payment? This will not affect batch closures nor cash closures and the amount will not be taking on account'); ?>
                        <br>
                        <label> <?= EcopagosModule::t('app', 'Set a justification for cancellation').':'?> </label>
                        <input class="form-control input-cause" type="text" id="input-cause-reverse">
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="btn-cancel-payout" type="button" class="btn btn-danger" id="confirmCancel">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?= EcopagosModule::t('app', 'Confirm payout reverse'); ?>
                        </a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal"><?= EcopagosModule::t('app', 'Close'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Reverse Modal -->

<script>
    var PayoutIndex = new function () {
        var id = '';

        this.init = function () {
            $("#btn-cancel-payout").addClass('disabled');

            $(document).on('click', '.cancelButton', function (e) {
                id = $(this).data('id');
                $('#reverse-modal').modal();
            });

            $("#input-cause-reverse").on("keyup", function () {
                if ($('#input-cause-reverse').val() == '') {
                    $("#btn-cancel-payout").addClass('disabled');
                } else {
                    $("#btn-cancel-payout").removeClass('disabled');
                };
            });

            $("#btn-cancel-payout").on('click', function(){
                PayoutIndex.reverseAndJustify( id, $('#input-cause-reverse').val(), false);
            });

        }

        this.reverseAndJustify = function (id, cause, reprint) {
            $.ajax({
                url: "<?= \yii\helpers\Url::toRoute('reverse') ?>",
                method: 'get',
                dataType: 'json',
                data: {id: id , cause: cause, reprint: reprint}
            });
        };
    }
</script>

<?php $this->registerJs('PayoutIndex.init()'); ?>