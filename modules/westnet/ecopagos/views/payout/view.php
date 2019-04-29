<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\westnet\ecopagos\EcopagosModule;
use yii\grid\GridView;
use app\modules\westnet\ecopagos\models\Justification;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Payout */

$this->title = $model->payout_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Payouts in Ecopagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payout-view">

    <div class="row">
        <div class="col-sm-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-sm-6 text-right">
            <a class="btn btn-primary" href="<?= yii\helpers\Url::to(['/checkout/payment/view', 'id' => $model->payment_id]); ?>">
                <span class="glyphicon glyphicon-bookmark"></span> <?= EcopagosModule::t('app', 'View Payment'); ?>
            </a>
        </div>
    </div>

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

    <h4><?= EcopagosModule::t('app', 'Re-prints and cancelled') ?></h4><br>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'type',
                'value' => function($model) {
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
