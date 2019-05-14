<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCardFile */

$this->title = Yii::t('app', 'Update Payment Card File: ' . $model->payment_card_file_id, [
    'nameAttribute' => '' . $model->payment_card_file_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Card Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->payment_card_file_id, 'url' => ['view', 'id' => $model->payment_card_file_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="payment-card-file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
