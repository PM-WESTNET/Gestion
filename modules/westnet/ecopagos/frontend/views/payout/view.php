<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\config\models\Config;
use yii\grid\GridView;
use app\modules\westnet\ecopagos\models\Justification;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Payout */

$this->registerJsFile("//www.java.com/js/deployJava.js", [
    'position' => \yii\web\View::POS_BEGIN
]);


$this->title = EcopagosModule::t('app', 'Payout') . ' ' . $model->payout_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Payouts in Ecopagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payout-view container bg-white">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>

        <?php
        if ($model->isValid() || $model->isClosed()) :
            echo Html::a('<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Print bill'), '#', [
                'class' => 'btn btn-info margin-right-quarter',
                'data-toggle' => 'modal',
                'data-target' => '#print-justification-modal',
                'id' => 'btn-print'
            ]);
        endif;

        if ($model->isReversable()) :
            echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . EcopagosModule::t('app', 'Reverse payout'), '#', [
                'class' => 'btn btn-danger',
                'data-toggle' => 'modal',
                'data-target' => '#reverse-modal',
            ]);
        endif;
        ?>

    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'payout_id',
            [
                'label' => EcopagosModule::t('app', 'Status'),
                'value' => EcopagosModule::t('app', $model->fetchStatuses()[$model->status])
            ],
            [
                'label' => EcopagosModule::t('app', 'Customer'),
                'value' => empty($model->customer) ? '' : $model->customer->name . ' ' . $model->customer->lastname,
            ],
            [
                'label' => EcopagosModule::t('app', 'Ecopago branch'),
                'value' => $model->ecopago->name
            ],
            [
                'label' => EcopagosModule::t('app', 'Cashier'),
                'value' => $model->cashier->getCompleteName()
            ],
            'amount:currency',
            'date',
            'time',
        ],
    ])
    ?>

    <?=
    $this->render('_printable_bill', [
        'model' => $model,
    ])
    ?>
    <div class="reprint no-print">
        <h4><?= EcopagosModule::t('app', 'Re-prints and cancelled') ?></h4><br>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        if ($model->type == Justification::TYPE_CANCELLATION) {
                            return '<label style="color: red;">' . EcopagosModule::t('app', $model->type) . '</label>';
                        }
                        return EcopagosModule::t('app', $model->type);
                    },
                    'format' => 'raw',
                ],
                'cause',
                'date'
            ]
        ]);
        ?>
    </div>
    
</div>

<!--MODALS-->

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
                <label> <?= EcopagosModule::t('app', 'Set a justification for cancellation') . ':' ?> </label>
                <input class="form-control input-cause" type="text" id="input-cause-reverse">
                <label id="reverse-label"></label>
            </div>
            <div class="modal-footer">
                <a href="#" id="btn-cancel-payout" type="button" class="btn btn-danger">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?= EcopagosModule::t('app', 'Confirm payout reverse'); ?>
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= EcopagosModule::t('app', 'Close'); ?></button>
            </div>
        </div>
    </div>
</div>
 <!-- end Reverse Modal -->

 <!-- Print Modal Justification-->
    <div class="modal fade" id="print-justification-modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?= EcopagosModule::t('app', 'Justification for re-print'); ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <label> Ingrese una justificación para la re-impresión:</label>
                    <input class="form-control" type="text" id="input-cause">
                    <label id="justification-label"> </label>
                </div>
                <div class="modal-footer">
                    <?=
                    Html::a('<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Print bill'), '#', [
                        'class' => 'btn btn-info margin-right-quarter',
                        'data-toggle' => 'modal',
                        'data-target' => '#print-modal',
                        'id' => 'btn-justify'
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
 <!-- end Reverse Modal Justification-->

<!-- Print Modal -->
<div class="modal fade" id="print-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="text-primary glyphicon glyphicon-print"></span>
                    <?= EcopagosModule::t('app', 'Printing ticket'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?= EcopagosModule::t('app', 'Please wait until the ticket is completely printed.'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                        id="close-modal"><?= EcopagosModule::t('app', 'Close') . ' (ESC)' ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end Print Modal -->

<script>
    var PayoutView = new function () {
        this.init = function () {
            $('#btn-cancel-payout').on('click', function () {
                PayoutView.reverseAndJustify($('#input-cause-reverse').val(), false);
            });

            $('#close-modal').on('click', function(){
                $("#print-modal").modal('hide');
                location.reload();
            });

            $("#btn-justify").on('click', function (evt) {
                evt.preventDefault();
                PayoutView.justify($('#input-cause').val(), true);
                $("#print-justification-modal").modal('hide');
            });

            $("#btn-justify").addClass('disabled');
            $("#btn-cancel-payout").addClass('disabled');

            $("#input-cause").on("keyup", function () {
                total = document.getElementById('input-cause').value.length;
                if (total <= <?= $min_justification_length ?>) {
                    $("#btn-justify").addClass('disabled');
                    <?php $text = EcopagosModule::t('app', 'Remaining characters: ');?>
                    $('#justification-label').text('<?=$text?>' + (<?=$min_justification_length?> -total));
                } else {
                    $("#btn-justify").removeClass('disabled');
                    $('#justification-label').text('');
                };
            });

            $("#input-cause-reverse").on("keyup", function () {
                total = document.getElementById('input-cause-reverse').value.length;
                if (total <= <?= $min_justification_length ?>) {
                    $("#btn-cancel-payout").addClass('disabled');
                    <?php $text = EcopagosModule::t('app', 'Remaining characters: ');?>
                    $('#reverse-label').text('<?=$text?>' + (<?=$min_justification_length?> -total));
                } else {
                    $('#reverse-label').text('');
                    $("#btn-cancel-payout").removeClass('disabled');
                };
            });
        };

        this.reverseAndJustify = function (cause, reprint) {
            $.ajax({
                url: "<?= \yii\helpers\Url::toRoute('reverse') ?>",
                method: 'get',
                dataType: 'json',
                data: {id: <?= $model->payout_id ?>, cause: cause, reprint: reprint}
            });
        };

        this.justify = function (cause, reprint) {
            $.ajax({
                url: "<?= \yii\helpers\Url::toRoute('save-justification') ?>",
                method: 'post',
                dataType: 'json',
                data: {payout_id: <?= $model->payout_id ?>, cause: cause, reprint: reprint}
            }).done(function (response) {
                if (reprint) {
                    PayoutView.print();
                    PayoutView.incrementCopyNumber();
                }
            });
        };

        this.incrementCopyNumber = function(){
            $.ajax({
                url: "<?= \yii\helpers\Url::toRoute('increment-copy-number') ?>",
                method: 'get',
                dataType: 'json',
                data: {payout_id: <?= $model->payout_id ?>}
            }).done(function (response){
                console.log(response);
            });
        }

        this.print = function () {
            Payout.printTicket(<?= $model->getPrintLayout(); ?>, "PayoutView.printCallback", '<?= Config::getConfig('chrome_print_app')->value; ?>');

        }

        this.printCallback = function (response) {
            if ((response && (response.status !== 'success'))) {
                alert('Ocurrio un error al imprimir');
            }
            $('#print-modal').modal('hide');
        }
    };
</script>

<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
</style>

<?php
$this->registerJs("PayoutView.init()");
?>