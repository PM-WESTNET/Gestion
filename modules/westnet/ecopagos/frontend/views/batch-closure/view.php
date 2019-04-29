<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\config\models\Config;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\BatchClosure */

$this->title = EcopagosModule::t('app', 'Batch closure') . ' ' . $model->batch_closure_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Batch Closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$collector = new \app\modules\westnet\ecopagos\models\Collector;
$collector->scenario = \app\modules\westnet\ecopagos\models\Collector::SCENARIO_VALIDATE_PASSWORD;
?>

<div class="batch-closure-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <a class="btn btn-primary" href="<?= yii\helpers\Url::to(['payout/index', 'PayoutSearch[batch_closure_id]' => $model->batch_closure_id]); ?>">
            <span class="glyphicon glyphicon-list"></span> <?= EcopagosModule::t('app', 'View payout list'); ?>
        </a>

        <?php
        if ($model->isRenderable()) :
            echo Html::a('<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Print bill'), '#', [
                'class' => 'btn btn-info margin-right-quarter',
                'data-toggle' => 'modal',
                'data-target' => '#print-modal',
            ]);
        endif;
        ?>

        <?php
        if ($model->isCancelable())
            echo Html::a('<span class="glyphicon glyphicon-remove"></span> ' . EcopagosModule::t('app', 'Cancel batch closure'), '#', [
                'class' => 'btn btn-danger',
                'data-toggle' => 'modal',
                'data-target' => '#cancel-modal',
            ])
            ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'batch_closure_id',
            [
                'label' => EcopagosModule::t('app', 'Ecopago'),
                'value' => $model->ecopago->name,
            ],
            [
                'format' => 'html',
                'label' => EcopagosModule::t('app', 'Last Batch closure'),
                'value' => (!empty($model->lastBatchClosure)) ? Yii::$app->formatter->asDatetime($model->lastBatchClosure->datetime) . ' (<strong>' . $model->last_batch_closure_id . '</strong>)' : EcopagosModule::t('app', 'None'),
            ],
            [
                'format' => 'html',
                'label' => EcopagosModule::t('app', 'Collector'),
                'value' => $model->collector->name . ' ' . $model->collector->lastname . ' (<strong>' . $model->collector->number . '</strong>)',
            ],
            [
                'format' => 'html',
                'label' => EcopagosModule::t('app', 'Date'),
                'value' => Yii::$app->formatter->asDatetime($model->datetime),
            ],
            'payment_count',
            [
                'label' => EcopagosModule::t('app', 'Raw total'),
                'value' => Yii::$app->formatter->asCurrency($model->total),
            ],
            [
                'label' => EcopagosModule::t('app', 'Commission'),
                'value' => Yii::$app->formatter->asCurrency($model->commission),
            ],
            [
                'label' => EcopagosModule::t('app', 'Discount'),
                'value' => Yii::$app->formatter->asCurrency($model->discount),
            ],
            [
                'label' => EcopagosModule::t('app', 'Net total'),
                'value' => Yii::$app->formatter->asCurrency($model->netTotal),
            ],
        ],
    ])
    ?>

</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= EcopagosModule::t('app', 'Confirm batch closure cancelation'); ?></h4>
            </div>
            <div class="modal-body">

                <?php
                $form = ActiveForm::begin([
                            'id' => 'form-batch-closure'
                ]);
                ?>
                <?=
                $this->render('_collector_login_form', [
                    'form' => $form,
                    'collector' => $collector
                ]);
                ?>
                <?php ActiveForm::end(); ?>
                <span class="text-danger font-bold">
                    <?= EcopagosModule::t('app', 'Are you sure you want to cancel this batch closure?'); ?>
                </span>
            </div>
            <div class="modal-footer">
                <a data-batch-closure="delete" href="<?= yii\helpers\Url::to(['cancel', 'id' => $model->batch_closure_id]); ?>" type="button" class="btn btn-danger disabled">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?= EcopagosModule::t('app', 'Confirm batch closure cancelation'); ?>
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
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= EcopagosModule::t('app', 'Close') . ' (ESC)' ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end Print Modal -->

<script>
    var BatchClosureView = new function() {
        var self = this;
        this.init = function () {
            $(document).off("click", "[data-target='#print-modal']").on("click", "[data-target='#print-modal']", function () {
                setTimeout(function(){
                    BatchClosureView.print()
                }, 500);
            });
            if ('<?php echo (isset($from) ? $from : '*') ?>' == 'create') {
                $("[data-target='#print-modal']").trigger("click");
            }
        }

        //Tries to open the printer app
        this.print = function () {
            Payout.printTicket(<?= $model->getPrintLayout(); ?>, 'BatchClosureView.printCallback', '<?= Config::getConfig('chrome_print_app')->value; ?>');
        }

        this.printCallback = function(response) {
            if((response && (response.status !== 'success') )) {
                alert('Ocurrio un error al imprimir');
            }
            $('#print-modal').modal('hide');
        }
    }
</script>
<?php $this->registerJs("BatchClosureView.init()"); ?>

<?= $this->registerJs("BatchClosure.setFetchCollectorInfoUrl('" . yii\helpers\Url::to(['collector/get-collector-info']) . "');"); ?>
<?= $this->registerJs("BatchClosure.setFetchPreviewUrl('" . yii\helpers\Url::to(['batch-closure/get-preview']) . "');"); ?>

<?= $this->registerJs("BatchClosure.bindPermanentFocus();"); ?>