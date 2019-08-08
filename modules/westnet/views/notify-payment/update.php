<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NotifyPayment */

$this->title = Yii::t('app', 'Update Notify Payment: ' . $model->notify_payment_id, [
    'nameAttribute' => '' . $model->notify_payment_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notify Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->notify_payment_id, 'url' => ['view', 'id' => $model->notify_payment_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="notify-payment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
