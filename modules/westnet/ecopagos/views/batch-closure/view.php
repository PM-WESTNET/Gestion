<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\BatchClosure */

$this->title = EcopagosModule::t('app', 'Batch closure') . ' ' . $model->batch_closure_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Batch closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-closure-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <a class="btn btn-primary" href="<?= yii\helpers\Url::to(['payout/payout-list', 'PayoutSearch[batch_closure_id]' => $model->batch_closure_id]); ?>">
                <span class="glyphicon glyphicon-list"></span> <?= EcopagosModule::t('app', 'View payout list'); ?>
            </a>
            <?php if ($model->isRenderable()) : ?>
                <a class="btn btn-success" href="<?= yii\helpers\Url::to(['render', 'id' => $model->batch_closure_id]); ?>">
                    <span class="glyphicon glyphicon-ok-sign"></span> <?= EcopagosModule::t('app', 'Render batch closure'); ?>
                </a>
            <?php endif; ?>
            <?php if ($model->isCancelable()) : ?>
                <a class="btn btn-danger" href="<?= yii\helpers\Url::to(['cancel', 'id' => $model->batch_closure_id]); ?>">
                    <span class="glyphicon glyphicon-ban-circle"></span> <?= EcopagosModule::t('app', 'Cancel batch closure'); ?>
                </a>
            <?php endif; ?>
        </p>
    </div>

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
                'label' => EcopagosModule::t('app', 'Status'),
                'value' => EcopagosModule::t('app', $model->status),
            ],
            [
                'format' => 'html',
                'label' => EcopagosModule::t('app', 'Last Batch closure'),
                'value' => (!empty($model->lastBatchClosure)) ? date('d/m/Y H:i:s', $model->lastBatchClosure->datetime) . ' (<strong>' . $model->last_batch_closure_id . '</strong>)' : EcopagosModule::t('app', 'None'),
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
            [
                'attribute' => 'real_total',
                'format' => 'currency',
            ],
            [
                'attribute' => 'difference',
                'format' => 'currency',
            ],
        ],
    ])
    ?>

</div>