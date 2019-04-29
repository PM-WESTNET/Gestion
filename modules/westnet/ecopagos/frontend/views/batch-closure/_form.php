<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\ecopagos\EcopagosModule;
use \app\modules\westnet\ecopagos\models\Collector;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use \app\modules\westnet\ecopagos\models\Ecopago;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\BatchClosure */
/* @var $form yii\widgets\ActiveForm */

$collector = new Collector;
$collector->scenario = Collector::SCENARIO_VALIDATE_PASSWORD;
?>

<div class="batch-closure-form margin-top-full">

    <?php
    $form = ActiveForm::begin([
                'id' => 'form-batch-closure'
    ]);
    ?>

    <!-- Collector information -->
    <?php if ($model->isNewRecord) : ?>
        <div id="collector-overlay" class="overlay"></div>
    <?php endif; ?>

    <div id="collector-info" class="panel panel-default position-relative z-depth-1 z-depth-important" style="z-index: 11;">

        <div class="panel-heading">
            <h3 class="panel-title"><?= EcopagosModule::t('app', 'Information about Collector'); ?></h3>
        </div>
        <div class="panel-body">

            <p class="text-muted"><?= EcopagosModule::t('app', 'Input collector number and password, and then press the Enter key'); ?></p>

            <input type='text' style='display: none'>
            <input type='password' style='display: none'>

            <div class="row">
                <div class="col-lg-6">
                    <?=
                    $form->field($collector, 'number')->textInput([
                        'maxlength' => 50,
                        'autocomplete' => 'false',
                        'autocomplete' => 'off',
                    ])
                    ?>
                </div>
                <div class="col-lg-6">
                    <?=
                    $form->field($collector, 'password')->passwordInput([
                        'maxlength' => 50,
                        'autocomplete' => 'false',
                        'autocomplete' => 'off',
                    ])
                    ?>
                </div>
            </div>

            <div class="collector-error no-margin-bottom">

            </div>

        </div>

    </div>
    <!-- end Collector information -->

    <!-- Main ecopago & last batch closure information -->
    <div id="ecopago-info" class="panel panel-default z-depth-1 z-depth-important">

        <div class="panel-heading">
            <h3 class="panel-title"><?= EcopagosModule::t('app', 'Information about Ecopago'); ?></h3>
        </div>

        <div class="panel-body">

            <div class="row">
                <div class="col-lg-12">
                    <?=
                    $form->field($model, 'ecopago_id')->dropdownList(ArrayHelper::map(Ecopago::find()->all(), 'ecopago_id', 'name'), [
                        'encode' => false,
                        'separator' => '<br/>',
                        'prompt' => 'Select an option...',
                        'disabled' => 'disabled',
                    ])
                    ?>
                </div>
            </div>

            <?php if (!empty($model->lastBatchClosure)) : ?>
                <div class="row">

                </div>
            <?php endif; ?>

        </div>
    </div>  
    <!-- end Main ecopago & last batch closure information -->

    <!-- Batch Closure Preview -->
    <div id="batch-closure-preview">
        
    </div>
    <!-- end Batch Closure Preview -->

    <div class="form-group">
        <a data-batch-closure="view-details" href="#!" class="btn btn-info disabled">
            <?= EcopagosModule::t('app', 'View batch closure details'); ?>
        </a>
        <?=
        Html::submitButton(EcopagosModule::t('app', 'eeExecute batch closure'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            'id' => 'batch-closure-submit',
            'disabled' => 'disabled',
        ])
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?= $this->registerJs("BatchClosure.setFetchCollectorInfoUrl('" . Url::to(['collector/get-collector-info']) . "');"); ?>
<?= $this->registerJs("BatchClosure.setFetchPreviewUrl('" . Url::to(['batch-closure/get-preview']) . "');"); ?>

<?= $this->registerJs("BatchClosure.bindPermanentFocus();"); ?>
<?= $this->registerJs("BatchClosure.fetchPreview();"); ?>
