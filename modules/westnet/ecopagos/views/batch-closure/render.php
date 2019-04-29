<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\helpers\CountableHelper;

$this->title = EcopagosModule::t('app', 'Render batch closure') . ' ' . $model->batch_closure_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Batch closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Batch closure') . ' ' . $model->batch_closure_id, 'url' => ['view', 'id' => $model->batch_closure_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-closure-render">

    <!-- Title -->
    <h1>

        <?= Html::encode($this->title) ?>

        <small class="display-block margin-top-quarter">
            <?= EcopagosModule::t('app', 'Rendering a batch closure involves a collector giving away all the money that was collected on a specific batch closure'); ?>
        </small>

    </h1>
    <!-- end Title -->

    <?php $form = ActiveForm::begin(); ?>

    <!-- Collector details -->
    <div class="panel panel-default z-depth-1 z-depth-important margin-top-half">

        <div class="panel-heading">
            <h3 class="panel-title"><?= app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Collector info'); ?></h3>
        </div>
        <div class="panel-body">

            <ul class="list-group no-margin-bottom">
                <li class="list-group-item">
                    <span class="badge"><?= $model->collector->getFormattedName(); ?></span>
                    <strong class="text-primary"><?= $model->collector->getAttributeLabel("name"); ?></strong>
                </li>
            </ul>

        </div>

    </div>
    <!-- end Collector details -->

    <!-- Batch closure details -->
    <div class="panel panel-default z-depth-1 z-depth-important">
        <div class="panel-heading">
            <h3 class="panel-title"><?= app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Batch closure details'); ?></h3>
        </div>
        <div class="panel-body">

            <ul class="list-group no-margin-bottom">
                <li class="list-group-item">
                    <span class="badge"><?= $model->ecopago->name; ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("ecopago"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asDatetime($model->datetime); ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("date"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= $model->payment_count; ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("payment_count"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asDatetime(($model->firstPayout  ? $model->firstPayout->datetime : '' )); ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("first_payout"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asDatetime(($model->lastPayout  ? $model->lastPayout->datetime : '' )); ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("last_payout"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asCurrency($model->total) ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("total"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asCurrency($model->commission) ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("commission"); ?></strong>
                </li>
                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asCurrency($model->netTotal) ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("net_total"); ?></strong>
                </li>
            </ul>

        </div>
    </div>
    <!-- end Batch closure details -->

    <?= Html::activeHiddenInput($model, 'batch_closure_id'); ?>

    <!-- Real amount -->
    <div class="panel panel-danger z-depth-1 z-depth-important">

        <div class="panel-heading">
            <h3 class="panel-title">
                <?= app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Rendered money'); ?>
            </h3>
        </div>

        <div class="panel-body">

            <!-- Money box selection -->

            <div class="row">
                <div class="col-lg-12">
                    <?=
                    $form->field($model, 'money_box_account_id')->dropDownList(\yii\helpers\ArrayHelper::map(CountableHelper::fetchEcopagoBanks(), 'money_box_account_id', 'number'), [
                        'prompt' => EcopagosModule::t('app', 'Select an option...'),
                    ])
                    ?>
                </div>
            </div>
            <!-- end Money box selection -->

            <!-- Totals and differences -->
            <div class="row">

                <div class="col-lg-6">
                    <?= $form->field($model, 'real_total')->textInput() ?>
                </div>

                <div class="col-lg-6">
                    <?= $form->field($model, 'difference')->textInput([
                        'readonly' => 'readonly'
                    ]) ?>
                </div>

            </div>
            <!-- end Totals and differences -->            

        </div>
    </div>
    <!-- end Real amount -->

    <div class="form-group">
        <?= Html::submitButton('<span class="glyphicon glyphicon-ok-sign"></span> ' . EcopagosModule::t('app', 'Render batch closure'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

    var realTotal = <?= $model->total; ?>

    function onReady() {

        var $inputRealTotal = $("[name='BatchClosure[real_total]']");
        var $inputDifference = $("[name='BatchClosure[difference]']");

        $inputRealTotal.on("change", function () {            
            
            var difference = realTotal - $(this).val();
            difference = Math.round(difference * 100) / 100;
            
            $inputDifference.val(difference);
            
        });

    }

</script>

<?= $this->registerJs("onReady();"); ?>