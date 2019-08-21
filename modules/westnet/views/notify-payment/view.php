<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\NotifyPayment */

$this->title = Yii::t('app', 'Notify Payment') .' '. $model->notify_payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notify payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notify-payment-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            [
                'attribute' => 'date',
                'value' => function($model) {
                    return date('d-m-Y',strtotime( $model->date));
                }
            ],
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
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return $model->created_at ? (new \DateTime('now'))->setTimestamp($model->created_at)->format('d-m-Y H:i') : '00-00-00 00:00';
                }
            ],
            [
                'attribute' => 'image_receipt',
                'value' => function($model) {
                    return Html::img($model->image_receipt, ['class' => 'img-responsive']);
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

</div>
