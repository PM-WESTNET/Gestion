<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\checkout\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NotifyPayment */

$this->title = Yii::t('app', 'Notify Payment') .' '. $model->notify_payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notify payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$payment_method_tranferencia = PaymentMethod::getTransferencia();
if($payment_method_tranferencia) {
    $payment_method_tranferencia_id = $payment_method_tranferencia->payment_method_id;
} else {
    $payment_method_tranferencia_id = 0;
}
?>
<div class="notify-payment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p> <?php if($model->verified != 1 && $model->payment_method_id == $payment_method_tranferencia_id) {
            echo Html::a(Yii::t('app', 'Mark as verified'), ['/westnet/notify-payment/verify', 'notify_payment_id' => $model->notify_payment_id], [
                'class' => 'btn btn-warning pull-right',
                'data-confirm' => Yii::t('app', 'Are you sure you want to mark it as verified?')
            ]);
        }?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'notify_payment_id',
            [
                'attribute' => 'customer_id',
                'value' => function($model) {
                    return Html::a($model->customer->fullName, ['/sale/customer/view', 'id' => $model->customer_id]);
                },
                'format' => 'raw'
            ],
            'date',
            [
                'attribute' => 'amount',
                'value' => function($model) {
                    return $model->amount ? Yii::$app->formatter->asCurrency($model->amount) : Yii::$app->formatter->asCurrency(0);
                }
            ],
            [
                'attribute' => 'payment_method_id',
                'value' => function($model) {
                    return $model->payment_method_id ? $model->paymentMethod->name : '';
                }
            ],
            'verified:boolean',
            [
                'attribute' => 'verified_by_user_id',
                'value' => function($model) {
                    return $model->verified_by_user_id ? $model->verifiedByUser->username : '';
                }
            ],
            [
                'attribute' => 'image_receipt',
                'value' => function($model) {
                    return Html::img($model->image_receipt, ['class' => 'img-responsive']);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return $model->created_at ? (new \DateTime('now'))->setTimestamp($model->created_at)->format('Y-m-d H:i') : '00-00-00 00:00';
                }
            ],
        ],
    ]) ?>

</div>
