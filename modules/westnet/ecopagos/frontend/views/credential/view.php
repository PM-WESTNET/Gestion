<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Credential */

$this->title = $model->credential_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Credentials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credential-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if ($model->deletable)
            echo Html::a('<span class=""></span>' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->credential_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => EcopagosModule::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'credential_id',
            [
                'label' => EcopagosModule::t('app', 'Customer'),
                'value' => EcopagosModule::t('app', $model->customer->name)
            ],
            [
                'label' => EcopagosModule::t('app', 'Cashier'),
                'value' => EcopagosModule::t('app', $model->cashier->getCompleteName())
            ],
            'datetime:datetime',
            [
                'label' => EcopagosModule::t('app', 'Status'),
                'value' => EcopagosModule::t('app', $model->fetchStatuses()[$model->status])
            ],
        ],
    ])
    ?>

</div>
