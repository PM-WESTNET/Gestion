<?php
/* @var $this View */
/* @var $model Payout */

use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\Payout;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = EcopagosModule::t('app', 'Create Payout');
?>

<?php if ($dailyClosure !== NULL) : ?>
    <div class="panel panel-default position-relative" style="z-index: 100; position: fixed; top: 50px; width: 100%; left: 0; padding-left: 0px;">

        <ul class="nav nav-pills position-relative z-depth-1 z-depth-important" style="z-index: 11;">
            <li role="presentation"><a><h5 class="no-margin">Pagos Registrados desde el último cierre: <?= $dailyClosure->payment_count ?></h5></a></li>
            <li role="presentation"><a><h5  class="no-margin">Último Pago Registrado: <?= $dailyClosure->last_payout_number ?></h5></a></li>
            <li role="presentation"><a><h5 class="no-margin">Total Cobrado : <?= Yii::$app->formatter->asCurrency($dailyClosure->total); ?></h5></a></li>
            <li role="presentation"><a><h5 class="no-margin">Cobros Cancelados:  <?= Payout::countReversed() ?></h5></a>
        </ul>
    </div>
<?php endif; ?>

<div class="container bg-white payout-create margin-top-full">


    <!-- Title and comment -->
    <h2 class="position-relative to-white font-white" style="z-index: 11;">
        <?= Html::encode($this->title) ?> -

        <small class="to-white font-white">
            <?php
            echo Yii::t('app', 'Ecopago') . ": " . $model->ecopago->name . " - " . EcopagosModule::t('app', 'Cashier') . ": " . $model->cashier->name;
            ?>
        </small>
    </h2>
    <!-- end Title and comment -->

    <?php if ($model->isNewRecord) : ?>
        <div id="customer-overlay" class="overlay"></div>
    <?php endif; ?>
    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>
    <br>


    <?php if (isset($oldModel) && isset($from)): ?>
        <div class="panel panel-info position-relative z-depth-1 z-depth-important" style="z-index: 11;">

            <div class="panel-heading" style="height: 40px; vertical-align: text-top; padding-top: 2px">
                <h5>Pago Anterior</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <label><?= Customer::findOne(['customer_id' => $oldModel->customer_id])->fullName ?> </label>
                    </div>
                    <div class="col-md-3">
                        <label><span class="font-size-s">Numero de Pago:</span> <?= $oldModel->payout_id ?></label>
                        <br>
                        <label><span class="font-size-s">Monto del Pago:</span> <?= Yii::$app->formatter->asCurrency($oldModel->amount) ?></label>
                    </div>
                    <div class="col-md-7">
                        <?php
                        echo Html::a('<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Print bill'), '#', [
                            'class' => 'btn btn-info margin-right-quarter hidden',
                            'data-toggle' => 'modal',
                            'data-target' => '#print-modal',
                            'id' => 'btn-print'
                        ]);
                        if ($oldModel->isValid() || $oldModel->isClosed()) :

                            echo Html::a('<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Print bill'), '#', [
                                'class' => 'btn btn-info margin-right-quarter',
                                'data-toggle' => 'modal',
                                'data-target' => '#print-justification-modal',
                                'id' => 'btn-observation-print'
                            ]);
                        endif;


                        if ($oldModel->isReversable()) :
                            echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . EcopagosModule::t('app', 'Reverse payout'), '#', [
                                'class' => 'btn btn-danger',
                                'data-toggle' => 'modal',
                                'data-target' => '#reverse-modal',
                            ]);
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>



    <?php endif; ?>

</div>

<?php if (isset($oldModel)): ?>
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
                    <label id="reverse-label">  </label>
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
                    <label id="justification-label"></label>
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

    <!-- Print Modal -->
    <div class="modal fade" id="print-modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="text-primary glyphicon glyphicon-print"></span>
                        <?= EcopagosModule::t('app', 'Printing ticket'); ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <?= EcopagosModule::t('app', 'Please wait until the ticket is completely printed.'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?= EcopagosModule::t('app', 'Close') . ' (ESC)' ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- end Print Modal -->

    <script>
        var PayoutCreate = new function () {
            var self = this;
            this.init = function () {

                $("#btn-justify").addClass('disabled');
                $("#btn-cancel-payout").addClass('disabled');

                $(document).off("keyup", "#input-cause").on("keyup", "#input-cause", function () {
                    total = document.getElementById('input-cause').value.length;
                    if (total <= <?= $min_justification_length ?>) {
                        $("#btn-justify").addClass('disabled');
                        <?php $text = EcopagosModule::t('app', 'Remaining characters: ') ;?>
                        $('#justification-label').text('<?=$text?>' + (<?=$min_justification_length?> - total));

                    } else {
                        $("#btn-justify").removeClass('disabled');
                        $('#justification-label').text('');
                    }
                });

                $(document).off("keyup", "#input-cause-reverse").on("keyup", "#input-cause-reverse", function () {
                    total = document.getElementById('input-cause-reverse').value.length;
                    if (total <= <?= $min_justification_length ?>) {
                        $("#btn-cancel-payout").addClass('disabled');
                        <?php $text = EcopagosModule::t('app', 'Remaining characters: ') ;?>
                        $('#reverse-label').text('<?=$text?>' + (<?=$min_justification_length?> - total));
                    } else {
                        $("#btn-cancel-payout").removeClass('disabled');
                        $('#reverse-label').text('');
                    }
                });

                $(document).off("click", "#btn-justify").on("click", "#btn-justify", function (evt) {
                    evt.preventDefault();
                    PayoutCreate.justify($('#input-cause').val(), true);
                    $("#print-justification-modal").modal('hide');
                });

                $(document).off("click", "#btn-cancel-payout").on("click", "#btn-cancel-payout", function (evt) {
                    evt.preventDefault();
                    PayoutCreate.reverseAndJustify($('#input-cause-reverse').val(), false);
                });
                $(document).off("click", "#btn-print").on("click", "#btn-print", function (evt) {
                    evt.preventDefault();
                    PayoutCreate.print();
                })

                <?php if ($oldModel && $oldModel->copy_number == 0) { ?>
                $("#btn-print").click();
                <?php } ?>
            }


            this.reverseAndJustify = function (cause, reprint) {
                $.ajax({
                    url: "<?= \yii\helpers\Url::toRoute('reverse') ?>",
                    method: 'get',
                    dataType: 'json',
                    data: {id: <?= $oldModel->payout_id ?>, cause: cause, reprint: reprint}
                }).done(function (response){
                    if(reprint){
                        PayoutCreate.print();
                    }
                });
            };

            this.justify = function (cause, reprint) {
                $.ajax({
                    url: "<?= \yii\helpers\Url::toRoute('save-justification') ?>",
                    method: 'post',
                    dataType: 'json',
                    data: {payout_id: <?= $oldModel->payout_id ?>, cause: cause, reprint: reprint}
                }).done(function (response){
                    if(reprint){
                        PayoutCreate.print();
                        PayoutCreate.incrementCopyNumber();
                    }
                });
            };

            this.incrementCopyNumber = function(){
                $.ajax({
                    url: "<?= \yii\helpers\Url::toRoute('increment-copy-number') ?>",
                    method: 'get',
                    dataType: 'json',
                    data: {payout_id: <?= $oldModel->payout_id ?>}
                }).done(function (response){
                    console.log(response);
                });
            }

            this.print = function () {
                Payout.printTicket(<?= $oldModel->getPrintLayout(); ?>, 'PayoutCreate.printCallback', '<?= Config::getConfig('chrome_print_app')->value; ?>');
            }

            this.printCallback = function(response) {
                if((response && (response.status !== 'success') )) {
                    alert('Ocurrio un error al imprimir');
                }
                $('#print-modal').modal('hide');
            }
        };

    </script>
    <?php $this->registerJs('PayoutCreate.init()'); ?>
<?php endif; ?>
