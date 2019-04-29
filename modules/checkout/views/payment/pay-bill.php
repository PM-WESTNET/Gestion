<?php

use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => Yii::t('app','Payment'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">

    <?php if($bill->hasCompletedPayment()): ?>
    <h3><?= Yii::t('app','Total payed') ?>: <strong> <?= Yii::$app->formatter->asCurrency($bill->getPayedAmount()); ?> </strong></h3>
    <h4><?= Yii::t('app','Payments detail') ?> </h4>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <td><?= Yii::t('app','Payment') ?></td>
                <td><?= Yii::t('app','Payment Method') ?></td>
                <td><?= Yii::t('app','Amount') ?></td>
                <td><?= Yii::t('app','Ticket Number') ?></td>
            </tr>
        </thead>
        <?php foreach($model->billHasPayments as $payment) {
            foreach ($payment->payment->paymentItems as $item) {
                ?>
                <tr>
                    <td><?= $payment->payment->payment_id; ?></td>
                    <td><?= $item->paymentMethod->name; ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($item->amount); ?></td>
                    <td><?= $payment->payment->number ?></td>
                </tr>
            <?php }
        }
        ?>

    </table>
        
    <?php endif; ?>
    
    

    <?= $this->render('_form_bill', [
        'model' => $model,
        'bill' => $bill,
    ]) ?>

</div>
