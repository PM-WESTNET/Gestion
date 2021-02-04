<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\DebitDirectFailedPayment */

$this->title = Yii::t('app', 'Update Debit Direct Failed Payment: ' . $model->debit_direct_failed_payment_id, [
    'nameAttribute' => '' . $model->debit_direct_failed_payment_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Debit Direct Failed Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->debit_direct_failed_payment_id, 'url' => ['view', 'id' => $model->debit_direct_failed_payment_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="debit-direct-failed-payment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
