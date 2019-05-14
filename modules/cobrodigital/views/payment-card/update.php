<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCard */

$this->title = Yii::t('app', 'Update Payment Card: ' . $model->payment_card_id, [
    'nameAttribute' => '' . $model->payment_card_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Cards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->payment_card_id, 'url' => ['view', 'id' => $model->payment_card_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="payment-card-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
