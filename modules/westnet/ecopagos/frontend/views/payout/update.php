<?php

use yii\helpers\Html;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Payout */

$this->title = EcopagosModule::t('app', 'Update payout') . ' | ' . $model->payout_id;
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Payouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Payout') . ' ' . $model->payout_id, 'url' => ['view', 'id' => $model->payout_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="container bg-white payout-update">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
