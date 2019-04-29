<?php

use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Withdrawal */

$this->title = EcopagosModule::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Withdrawal',
]) . ' ' . $model->withdrawal_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Withdrawals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->withdrawal_id, 'url' => ['view', 'id' => $model->withdrawal_id]];
$this->params['breadcrumbs'][] = EcopagosModule::t('app', 'Update');
?>
<div class="withdrawal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
