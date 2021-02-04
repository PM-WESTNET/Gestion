<?php

use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Html;
use app\components\helpers\UserA;

?>

<?php
if ($model->status == Contract::STATUS_ACTIVE) {
    echo UserA::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update-connection', 'id' => $model->contract_id], [
        'class' => 'btn btn-primary',
        'id' => 'update-connection',
    ]);

    echo UserA::a(Yii::t('westnet', 'Connection Forced Historials'), ['/westnet/connection-forced-historial/index', 'connection_id'=>$connection->connection_id], [
        'class' => 'btn btn-info',

    ]);

    if(User::canRoute('/sale/contract/contract/change-node')){
        echo UserA::a(Yii::t('westnet', 'Change Node'), null, [
            'class' => 'btn btn-warning',
            'id' => 'change-node',
        ]);
    }

    if(User::canRoute('/sale/contract/contract/change-ip')) {
        echo UserA::a(Yii::t('westnet', 'Change IP'), null, [
            'class' => 'btn btn-warning',
            'id' => 'change-ip',
        ]);
    }

    if ($connection->status == Connection::STATUS_ENABLED && User::canRoute('/westnet/connection/disable')) {
        echo Html::a(Yii::t('westnet', 'Disable'), null, [
            'class' => 'btn btn-danger',
            'id' => 'disable-connection',
            'data-loading-text' => Yii::t('westnet', 'Disabling') . "..."
        ]);
    }

    if ($connection->status == Connection::STATUS_DISABLED && User::canRoute('/westnet/connection/enable')) {
        echo UserA::a(Yii::t('westnet', 'Activate'), null, [
            'class' => 'btn btn-danger',
            'id' => 'enable-connection',
            'data-loading-text' => Yii::t('westnet', 'Enabling') . "..."
        ]);
    }

    if (User::canRoute('/westnet/connection/force')) {
        echo UserA::a(Yii::t('westnet', 'Force Activation'), null, [
            'class' => 'btn btn-danger',
            'id' => 'force-connection',
            'data-loading-text' => Yii::t('westnet', 'Enabling') . "..."
        ]);
    }
}
?>