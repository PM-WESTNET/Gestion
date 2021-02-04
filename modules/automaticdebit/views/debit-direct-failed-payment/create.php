<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\DebitDirectFailedPayment */

$this->title = Yii::t('app', 'Create Debit Direct Failed Payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Debit Direct Failed Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="debit-direct-failed-payment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
