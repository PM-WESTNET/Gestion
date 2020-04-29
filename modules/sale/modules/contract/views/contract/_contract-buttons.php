<?php

use app\modules\sale\modules\contract\models\Contract;
use app\components\helpers\UserA;

?>

<?php
    if ($model->status != Contract::STATUS_CANCELED && $model->canUpdate()) {
        echo UserA::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->contract_id], ['class' => 'btn btn-primary']);
    }

    if ($model->status == Contract::STATUS_LOW_PROCESS) {
        echo UserA::a(Yii::t('app', 'Reactive Contract'), ['active-contract-again', 'contract_id' => $model->contract_id], ['class' => 'btn btn-success']);
    }

    if (Yii::$app->getModule('westnet') && $model->status === Contract::STATUS_DRAFT && $model->canPrintAds()) {
        echo UserA::a(Yii::t('app', 'Print') . ' ADS', '#', ['class' => 'btn btn-success', 'id' => 'print-ads']);
    }

    if ($model->status == Contract::STATUS_DRAFT) {
        echo UserA::a(Yii::t('app', 'Active Contract'), ['active-contract', 'id' => $model->contract_id], ['class' => 'btn btn-success']);
    } elseif ($model->status == Contract::STATUS_ACTIVE && $model->getContractDetails()->filterWhere(['status' => Contract::STATUS_DRAFT])->count() > 0) {
        echo UserA::a(Yii::t('app', 'Active new items of Contract'), null, [
            'class' => 'btn btn-success',
            'data-loading-text' => Yii::t('app', 'Processing'),
            'id' => 'btn-active-new-items'
        ]);
    }

    if ($model->deletable)
        echo UserA::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->contract_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]);


    echo UserA::a(Yii::t('app', 'History'), ['history', 'id' => $model->contract_id], ['class' => 'btn btn-default']);


    if ($model->status == Contract::STATUS_ACTIVE) {
        echo UserA::a(Yii::t('app', 'Begin Low Process'), null, ['class' => 'btn btn-danger', 'id' => 'btn-low-process', 'data-id' => $model->contract_id]);
        echo UserA::a(Yii::t('app', 'Create programmed plan change'), ['programmed-plan-change/create', 'contract_id' => $model->contract_id], ['class' => 'btn btn-warning']);
        echo UserA::a(Yii::t('app', 'Update on ISP'), ['update-on-isp', 'contract_id' => $model->contract_id], ['class' => 'btn btn-warning', 'data-confirm' => '¿Está seguro que desea actualizar este contrato en el ISP?']);
    }
    if ($model->status == Contract::STATUS_LOW_PROCESS) {
        echo UserA::a(Yii::t('app', 'Definitive Low'), ['cancel-contract', 'id' => $model->contract_id], ['class' => 'btn btn-danger', 'id' => 'btn-definitive-low']);
        echo UserA::a(Yii::t('app', 'Update on ISP'), ['update-on-isp', 'contract_id' => $model->contract_id], ['class' => 'btn btn-warning', 'data-confirm' => '¿Está seguro que desea actualizar este contrato en el ISP?']);
    }
    if ($model->status == Contract::STATUS_DRAFT) {
        echo UserA::a(Yii::t('app', 'No want the Service'), ['rejected-service', 'id' => $model->contract_id, 'type' => Contract::STATUS_NO_WANT], ['class' => 'btn btn-danger']);
    }
    if ($model->status == Contract::STATUS_DRAFT) {
        echo UserA::a(Yii::t('app', 'Negative Survey'), ['rejected-service', 'id' => $model->contract_id, 'type' => Contract::STATUS_NEGATIVE_SURVEY], ['class' => 'btn btn-danger']);
    }
    if ($model->status == Contract::STATUS_NEGATIVE_SURVEY) {
        echo UserA::a(Yii::t('app', 'Revert negative survey'), ['revert-negative-survey', 'contract_id' => $model->contract_id], ['class' => 'btn btn-danger']);
    }
?>