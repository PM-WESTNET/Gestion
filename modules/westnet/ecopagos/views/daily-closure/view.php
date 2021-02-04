<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\DailyClosure */

$this->title = $model->daily_closure_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Daily closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daily-closure-view">

    <h1><?= EcopagosModule::t('app', 'Daily closure') . ' ' . Html::encode($this->title) ?></h1>

    <p> 
        <a class="btn btn-primary" href="<?= yii\helpers\Url::to(['payout/daily-payout-list', 'PayoutSearch[daily_closure_id]' => $model->daily_closure_id]); ?>">
            <span class="glyphicon glyphicon-list"></span> <?= EcopagosModule::t('app', 'View payout list'); ?>
        </a>
    </p>

    <?=
    DetailView::widget([
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
                'value' => date('d/m/Y H:i:s', $model->datetime),
            ],
            'payment_count',
            [
                'label' => EcopagosModule::t('app', 'Raw total'),
                'value' => '$ ' . $model->total,
            ],
        ],
    ])
    ?>

</div>
