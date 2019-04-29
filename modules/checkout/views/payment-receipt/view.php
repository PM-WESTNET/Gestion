<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\PaymentReceipt */

$this->title = Yii::t('app','Payment receipt') .' '. $model->payment_receipt_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Receipts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-receipt-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->payment_receipt_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->payment_receipt_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer',
                'value' => $model->customer ? $model->customer->fullName : ''
            ],
            'date:date',
            [
                'attribute' => 'paymentMethod.name',
                'label' => Yii::t('app', 'Payment Method'),
            ],
            [
                'label' => Yii::t('app', 'Concept'),
                'value'=> $model->concept

            ],
            'amount:currency'
        ],
    ]) ?>

</div>
    <script>
        var PaymentView = new function(){
            this.init = function() {
                $($(".glyphicon-print").parent()).each(function(){
                    this.onclick=function(){
                        window.open("<?=Url::toRoute(['payment-receipt/pdf', 'id'=>$model->payment_receipt_id])?>");
                    };
                })
            }
        };
    </script>
<?php
if($model->paymentMethod->type!="account") {
    $this->registerJs('PaymentView.init();');
}
?>