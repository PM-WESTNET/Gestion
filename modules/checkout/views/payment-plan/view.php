<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\checkout\models\Payment */

$this->title = Yii::t('app','Payment').' '.$model->payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
            if ($model->status=='draft') {
                echo Html::a("<span class='glyphicon glyphicon-plus'></span> " .Yii::t('app', 'Update'), ['update', 'id' => $model->payment_id], ['class' => 'btn btn-success']);
                echo Html::a("<span class='glyphicon glyphicon-plus'></span> " .Yii::t('app', 'Apply to Bill'), ['apply', 'id' => $model->payment_id], ['class' => 'btn btn-success']);
            }
            if ($model->canClose()) {
                echo Html::a("<span class='glyphicon glyphicon-repeat'></span> " .Yii::t('app', 'Close'), ['close', 'payment_id' => $model->payment_id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to close the Payment?'),
                        'method' => 'post',
                    ],
                ]);
            }
            if($model->deletable){
                echo Html::a("<span class='glyphicon glyphicon-remove'></span> " .Yii::t('app', 'Delete'), ['delete', 'id' => $model->payment_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }
            ?>
        </p>
    </div>

        <?php

        $attributes = [];
        if (Yii::$app->params['companies']['enabled']) {
            $attributes[] = [
                'label' => Yii::t('app', 'Company'),
                'value' => $model->company_id ? $model->company->name: ''
            ];
        }
        $attributes = array_merge($attributes, [
            [
                'attribute' => 'customer',
                'value' => $model->customer ? $model->customer->fullName : ''
            ],
            'date:date',
            'number',
            [
                'attribute' => 'status',
                'value' => Yii::t('app', ucfirst($model->status))
            ],
            'amount:currency',
            'balance:currency'
        ]);

        echo DetailView::widget([
            'model' => $model,
            'attributes' => $attributes]); ?>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Detail') ?></h3>
        </div>
        <div class="panel-body">
            <?php
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getPaymentItems(),
            ]);

            echo GridView::widget([
                'id'=>'grid',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('app', 'Payment Method'),
                        'value' => function($model){
                            return $model->paymentMethod->name .
                            ($model->moneyBoxAccount ? " - " . $model->moneyBoxAccount->moneyBox->name : '' ) .
                            ($model->moneyBoxAccount ? " - " . $model->moneyBoxAccount->number : '' ) .
                            ($model->number ? " - " . $model->number : '' ) ;
                        },
                    ],
                    'description',
                    'amount:currency',
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Bills') ?></h3>
        </div>
        <div class="panel-body">
            <?php
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getBillHasPayments(),
            ]);

            echo GridView::widget([
                'id'=>'grid',
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t('app', 'Bill'),
                        'value' => function($model) {
                            return ($model->bill ? ($model->bill->billType ? $model->bill->billType->name . " - " : "") . $model->bill->number : "");
                        }
                    ],
                    'bill.date',
                    [
                        'attribute' => 'bill.total',
                        'label'     => Yii::t('app', 'Total'),
                        'format'    => ['currency']
                    ],
                    [
                        'attribute' => 'amount',
                        'label'     => Yii::t('app', 'Amount applied'),
                        'format'    => ['currency']
                    ],
                    [
                        'attribute' => 'bill.debt',
                        'label'     => Yii::t('app', 'Balance'),
                        'format'    => ['currency']
                    ],
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>
</div>
<script>
    var PaymentView = new function(){
        this.init = function() {
            $($(".glyphicon-print").parent()).each(function(){
                this.onclick=function(){
                    window.open("<?=Url::toRoute(['payment/pdf', 'id'=>$model->payment_id])?>");
                };
            })
        }
    };
</script>
