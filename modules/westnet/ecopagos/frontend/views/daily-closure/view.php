<?php

use app\modules\config\models\Config;
use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\DailyClosure */

$this->title = $model->daily_closure_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Daily closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daily-closure-view">

    <h1><?= EcopagosModule::t('app', 'Daily closure'); ?> <?= Html::encode($this->title) ?></h1>

    <p>
        <a class="btn btn-primary" href="<?= yii\helpers\Url::to(['payout/index', 'PayoutSearch[daily_closure_id]' => $model->daily_closure_id]); ?>">
            <span class="glyphicon glyphicon-list"></span> <?= EcopagosModule::t('app', 'View payout list'); ?>
        </a>

        <?php
        
            echo Html::a('<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Imprimir Comprobante de Cierre Diario'), '#', [
                'class' => 'btn btn-info margin-right-quarter',
                'data-toggle' => 'modal',
                'data-target' => '#print-modal',
                'id' => 'btn-print'
            ]);
        
        if ($model->isCancelable())
            echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . EcopagosModule::t('app', 'Cancel daily closure'), '#', [
                'class' => 'btn btn-danger',
                'data-toggle' => 'modal',
                'data-target' => '#cancel-modal',
            ])
            ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'daily_closure_id',
            [
                'label' => EcopagosModule::t('app', 'Ecopago'),
                'value' => $model->ecopago->name,
            ],
            [
                'format' => 'html',
                'label' => EcopagosModule::t('app', 'Cashier'),
                'value' => $model->cashier->name . ' ' . $model->cashier->lastname,
            ],
            [
                'format' => 'html',
                'label' => EcopagosModule::t('app', 'Date'),
                'value' => Yii::$app->formatter->asDate($model->datetime),
            ],
            'payment_count',
            [
                'label' => EcopagosModule::t('app', 'Total'),
                'value' => Yii::$app->formatter->asCurrency($model->total),
            ],
        ],
    ]) ?>

</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= EcopagosModule::t('app', 'Confirm daily closure cancelation'); ?></h4>
            </div>
            <div class="modal-body">
                <span class="text-danger font-bold">
                    <?= EcopagosModule::t('app', 'Are you sure you want to cancel this daily closure?'); ?>
                </span>
            </div>
            <div class="modal-footer">
                <a data-batch-closure="delete" href="<?= yii\helpers\Url::to(['cancel', 'id' => $model->daily_closure_id]); ?>" type="button" class="btn btn-danger">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?= EcopagosModule::t('app', 'Confirm daily closure cancelation'); ?>
                </a>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= EcopagosModule::t('app', 'Close'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end Cancel Modal -->

<!-- Print Modal -->
<div class="modal fade" id="print-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="location.href= '<?= yii\helpers\Url::to(['site/index'])?>'"><?= EcopagosModule::t('app', 'Close') . ' (ESC)' ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end Print Modal -->

<script>
    var DailyClosureView = new function(){
        var self = this;

        this.init = function(){
            $(document).off("click", "#btn-print']").on("click", "#btn-print", function () {
                setTimeout(function(){
                    self.print()
                }, 500);
            });

            if('<?php if(isset($from)){echo $from;}else{echo'*';}?>'=='close'){
                $("#btn-print").trigger('click');
            }
        }

        this.print = function() {
            Payout.printTicket(<?= $model->getPrintLayout(); ?>, 'DailyClosureView.printCallback', '<?= Config::getConfig('chrome_print_app')->value; ?>');
        }

        this.printCallback = function(response) {
            if((response && (response.status !== 'success') )) {
                alert('Ocurrio un error al imprimir');
            }
            $('#print-modal').modal('hide');
        }
    };

</script>

<?php $this->registerJs('DailyClosureView.init()');?>