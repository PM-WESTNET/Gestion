<?php

use app\modules\checkout\models\PaymentPlan;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\checkout\models\search\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment Plans') . " - " . $customer->fullName;
$this->params['breadcrumbs'][] = ['label' => $customer->fullName, 'url' => ['/checkout/payment/current-account', 'customer'=>$customer->customer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', 'Create {modelClass}', [
                'modelClass' => Yii::t('app','Payment Plan'),
            ]), ['create', 'customer_id'=>$customer->customer_id], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?php

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'from_date:date',
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function($model) {
                    return Yii::t('app', ucfirst($model->status));
                },
            ],
            'original_amount:currency',
            'payment_plan_amount:currency',
            'fee',
            [
                'label' => Yii::t('app', 'Apply'),
                'value' => function($model) {
                    return ($model->apply  == 0 ? '' : ($model->apply==-1 ? Yii::t('app', 'Discount') : Yii::t('app', 'Surcharge') ) );
                },
            ],
            [
                'label' => Yii::t('app', 'Value Applied'),
                'value' => function($model) {
                    return $model->value_applied ." %";
                },
            ],
            'final_amount:currency',
            'balance:currency',            
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>'{cancel}',
                'buttons'=>[
                    'cancel'=>function ($url, $model, $key) use ($customer) {
                        if($model->status == PaymentPlan::STATUS_ACTIVE) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                yii\helpers\Url::toRoute(['/checkout/payment-plan/cancel', 'id' => $model->payment_plan_id, 'customer_id' => $customer->customer_id]),
                                [
                                    'title' => Yii::t('yii', 'Cancel'),
                                    'class' => 'updateItem btn btn-warning',
                                    'data-confirm' => Yii::t('westnet', 'Are you sure you want to cancel this plan?'),
                                    'data-method' => 'post',
                                ]);
                        }
                    },
                ]
          ]
        ],
    ]); ?>

</div>
