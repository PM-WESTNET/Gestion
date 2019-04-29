<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Ecopago */

$this->title = EcopagosModule::t('app', 'Manage collectors') . ' | ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ecopagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->ecopago_id]];
$this->params['breadcrumbs'][] = EcopagosModule::t('app', 'Manage collectors');

$collectors = app\modules\westnet\ecopagos\models\Collector::find()->all();

$collectorIDs = [];

if (!empty($model->collectors)) {
    foreach ($model->collectors as $key => $collector) {
        $collectorIDs[] = $collector->collector_id;
    }
}
?>

<div class="ecopago-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($collectors)) : ?>
        <?php $form = ActiveForm::begin(); ?>


        <div class="form-group field-ecopago-collectors">

            <h3>
                <?= EcopagosModule::t('app', 'Collectors'); ?>
                <small><?= EcopagosModule::t('app', 'Select all collectors that must be assignated to this Ecopago.'); ?></small>
            </h3>

            <input type="hidden" name="Ecopago[collectors]" value="">

            <div id="ecopago-collectors" class="row margin-top-full">

                <?php
                foreach ($collectors as $collector) :
                    $isAssignated = in_array($collector->collector_id, $collectorIDs);
                    ?>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="panel panel-<?= ($isAssignated) ? 'info' : 'default'; ?>">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <label>
                                        <input <?= ($isAssignated) ? 'checked' : ''; ?> type="checkbox" name="Ecopago[collectors][]" value="<?= $collector->collector_id; ?>"> <?= $collector->name; ?> <?= $collector->lastname; ?>
                                    </label>
                                </h3>
                            </div>
                            <div class="panel-body">

                                <ul class="list-group no-margin-bottom">

                                    <li class="list-group-item">

                                        <div class="row padding-bottom-quarter">

                                            <div class="col-lg-4">
                                                <span class="glyphicon glyphicon glyphicon-tag margin-right-quarter"></span> 
                                                <strong><?= $collector->getAttributeLabel('number'); ?></strong> 
                                            </div>

                                            <div class="col-lg-8">
                                                <span class="display-inline-block">
                                                    <?= $collector->number; ?>
                                                </span>
                                            </div>

                                        </div>

                                    </li>

                                    <li class="list-group-item">

                                        <div class="row padding-bottom-quarter">

                                            <div class="col-lg-4">
                                                <span class="glyphicon glyphicon glyphicon-book margin-right-quarter"></span> 
                                                <strong><?= $collector->document_type; ?></strong> 
                                            </div>

                                            <div class="col-lg-8">
                                                <span class="display-inline-block">
                                                    <?= $collector->document_number; ?>
                                                </span>
                                            </div>

                                        </div>

                                    </li>

                                    <li class="list-group-item">
                                        <div class="row padding-bottom-quarter">

                                            <div class="col-lg-4">
                                                <span class="glyphicon glyphicon glyphicon-usd margin-right-quarter"></span> 
                                                <strong><?= $collector->getAttributeLabel('limit'); ?></strong> 
                                            </div>

                                            <div class="col-lg-8">
                                                <span class="display-inline-block">
                                                    <?= Yii::$app->formatter->asCurrency($collector->limit) ?>
                                                </span>
                                            </div>

                                        </div>

                                    </li>

                                </ul>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

            <div class="help-block"></div>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    <?php else : ?>
        <p class="text-muted">
            <?= EcopagosModule::t('app', 'No collectors found.'); ?>
        </p>
    <?php endif; ?>

</div>