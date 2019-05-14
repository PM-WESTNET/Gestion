<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\cobrodigital\models\PaymentCard */

$this->title = Yii::t('app', 'Create Payment Card');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Cards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-card-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
