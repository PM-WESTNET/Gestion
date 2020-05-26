<?php

use app\modules\sale\models\ProductToInvoice;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Products to invoice to {customer}', ['customer' => $customer->name]);

$this->params['breadcrumbs'][] = ['label' => $customer->name, 'url' => ['/sale/customer/view', 'id'=> $customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title ;
?>
<div class="customer-has-discount-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Contract'),
                'value' => function($model) {
                    return ($model->contractDetail ? $model->contractDetail->contract->description : Yii::t('app', 'Payment Plan') );
                }
            ],
            [
                'label' => Yii::t('app', 'Product'),
                'value' => function($model) {
                    return ($model->contractDetail ? $model->contractDetail->product->name : Yii::t('app', 'Payment Plan') .  ($model->paymentPlan ? " - ".$model->paymentPlan->payment_plan_id  : '' ));
                }
            ],
            [
                'label' => Yii::t('app', 'Period'),
                'value' => function($model) {
                    return Yii::$app->formatter->asDate( $model->period, "M/Y");
                }
            ],
            [
                'label' => Yii::t('app', 'Created at'),
                'value' => function($model) {
                    return ($model->timestamp ? (new \DateTime())->setTimestamp($model->timestamp)->format('d/m/Y H:m') : '');
                }
            ],
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return Yii::t('app',  ucfirst($model->status));
                }
            ],
            'qty',
            'amount:currency',
            [
                'label'=> Yii::t('app', 'Total'),
                'value'=>function($model){
                    return Yii::$app->formatter->asCurrency($model->qty * $model->amount);
                }
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{view} {update} {cancel} {activate}',
                'buttons'=>[
                    'view'=>function ($url, $model, $key) use ($customer) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            yii\helpers\Url::toRoute(['/sale/product-to-invoice/view', 'id'=>$model->product_to_invoice_id, 'customer_id'=>$customer->customer_id]),
                            [
                                'title' => Yii::t('yii', 'View'),
                                'class' => 'deleteItem btn btn-view'
                            ]);
                    },
                    'update'=>function ($url, $model, $key) use ($customer) {
                        if($model->status != ProductToInvoice::STATUS_CANCELED && $model->status != ProductToInvoice::STATUS_CONSUMED && !$model->customer_id ) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                yii\helpers\Url::toRoute(['/sale/product-to-invoice/update', 'id' => $model->product_to_invoice_id, 'customer_id' => $customer->customer_id]),
                                [
                                    'title' => Yii::t('yii', 'Update'),
                                    'class' => 'updateItem btn btn-primary'
                                ]);
                        }
                    },
                    'cancel'=>function ($url, $model, $key) use ($customer) {
                        if($model->can(ProductToInvoice::STATUS_CANCELED)) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                yii\helpers\Url::toRoute(['/sale/product-to-invoice/cancel', 'id' => $model->product_to_invoice_id, 'customer_id' => $customer->customer_id]),
                                [
                                    'title' => Yii::t('yii', 'Cancel'),
                                    'class' => 'updateItem btn btn-warning'
                                ]);
                        }
                    },
                    'activate'=>function ($url, $model, $key) use ($customer) {
                        if($model->can(ProductToInvoice::STATUS_ACTIVE)) {
                            return Html::a('<span class="glyphicon glyphicon-ok"></span>',
                                yii\helpers\Url::toRoute(['/sale/product-to-invoice/activate', 'id' => $model->product_to_invoice_id, 'customer_id' => $customer->customer_id]),
                                [
                                    'title' => Yii::t('yii', 'Activate'),
                                    'class' => 'updateItem btn btn-warning'
                                ]);
                        }
                    }
                ]
            ],
        ],
    ]); ?>

</div>
