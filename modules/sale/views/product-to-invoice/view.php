<?php

use app\modules\sale\models\ProductToInvoice;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\CustomerHasDiscount */

$this->title = Yii::t('app', 'Product to Invoice') . " - " .
    ($model->contractDetail ? $model->contractDetail->product->name : Yii::t('app', 'Payment Plan') . ($model->paymentPlan ? " - " . $model->paymentPlan->payment_plan_id :  "")  );
$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/sale/customer/view', 'id'=> $customer->customer_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products to Invoice'), 'url' => ['/sale/product-to-invoice/index', 'customer_id'=> $customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;
?>
<div class="customer-has-discount-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php

        if($model->status != ProductToInvoice::STATUS_CANCELED && $model->status != ProductToInvoice::STATUS_CONSUMED && !$model->customer_id ) {
            echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->product_to_invoice_id, 'customer_id'=>$customer->customer_id], ['class' => 'btn btn-primary']);
        }

        if($model->can(ProductToInvoice::STATUS_CANCELED)) {
            echo Html::a(Yii::t('app', 'Cancel'), ['cancel', 'id' => $model->product_to_invoice_id, 'customer_id'=>$customer->customer_id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to cancel this {model}?', ['model'=>Yii::t('app', 'Product to Invoice')]),
                    'method' => 'post',
                ],
            ]);
        }
        if($model->can(ProductToInvoice::STATUS_ACTIVE)) {
            echo Html::a(Yii::t('app', 'Activate'), ['active', 'id' => $model->product_to_invoice_id, 'customer_id'=>$customer->customer_id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to activate this {model}?', ['model'=>Yii::t('app', 'Product to Invoice')]),
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('app', 'Contract'),
                'attribute' => function($model) {
                    return ($model->contractDetail ? $model->contractDetail->contract->description : Yii::t('app', 'Payment Plan') );
                }
            ],
            [
                'label' => Yii::t('app', 'Product'),
                'attribute' => function($model) {
                    return ($model->contractDetail ? $model->contractDetail->product->name : Yii::t('app', 'Payment Plan') . ($model->paymentPlan ? " - " . $model->paymentPlan->payment_plan_id : "" )  );
                }
            ],
            [
                'label' => Yii::t('app', 'Period'),
                'attribute' => function($model) {
                    return Yii::$app->formatter->asDate( $model->period, "M/Y");
                }
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'attribute'=>function($model){
                    return Yii::t('app',  ucfirst($model->status));
                }
            ],
            [
                'label' => Yii::t('app', 'Description'),
                'attribute'=>function($model){
                    return $model->description;
                }
            ],
            'qty',
            'amount:currency',
            [
                'label'=> Yii::t('app', 'Total'),
                'attribute'=>function($model){
                    return Yii::$app->formatter->asCurrency($model->qty * $model->amount);
                }
            ],
            [
                'label' => Yii::t('app', 'Discount'),
                'attribute'=>function($model){
                    return ($model->discount?  $model->discount->name . " - " .
                        Yii::t('app', $model->discount->type) . " - " . $model->discount->value
                        : Yii::t('app', 'No apply') );
                }
            ]

        ],
    ]) ?>

</div>
