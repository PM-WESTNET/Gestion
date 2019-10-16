<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use app\modules\sale\models\Customer;

$this->title = Yii::t('app','Import').': '.$import->bank->name. ' - '. Yii::$app->formatter->asDate($import->create_timestamp, 'dd-MM-yyyy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Banks for Automatic Debit'), 'url' => ['/automaticdebit/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $import->bank->name, 'url' => ['/automaticdebit/bank/view', 'id' => $import->bank->bank_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Imports'), 'url' => ['/automaticdebit/bank/imports', 'bank_id' => $import->bank->bank_id]];
$this->params['breadcrumbs'][] = Yii::$app->formatter->asDate($import->create_timestamp, 'dd-MM-yyyy');

$dataProviderPayments = new ActiveDataProvider(['query' => $import->getPayments()]);
$dataProviderFailedPayments = new ActiveDataProvider(['query' => $import->getFailedPayments()]);
?>

<div class="export">

    <h1 class="title"><?php echo $this->title ?></h1>

    <?= DetailView::widget([
        'model' => $import,
        'attributes' => [
            [
                'label' => Yii::t('app','Company'),
                'value' => function ($model) {
                    return $model->company->name;
                }
            ],
            'file'
        ]
    ])?>

    <h3> <?=Yii::t('app','Payments')?> </h3>
    <?php if($dataProviderPayments->count >= 1) {
        if($import->arePaymentPendingToClose()) {
            echo Html::a(Yii::t('app', 'Close all payments'), ['close-import', 'import_id' => $import->debit_direct_import_id], ['class' => 'btn btn-warning']);
        }

        echo GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $import->getPayments()]),
            'columns' => [
                [
                    'class' => SerialColumn::class],
                [
                    'attribute' => 'customer.fullName',
                    'label' => Yii::t('app', 'Customer'),
                    'value' => function ($model) {
                        return Html::a($model->customer->fullName, ['/sale/customer/view', 'id' => $model->customer_id]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'status',
                    'value' => function($model) {
                        return Yii::t('app', $model->status);
                    }
                ],
                [
                    'attribute' => 'amount',
                    'format' => 'currency'
                ],
                [
                    'class' => 'app\components\grid\ActionColumn',
                    'template' => '{view} {update} {delete} {pdf}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['/checkout/payment/view', 'id' => $key]), [
                                'title' => Yii::t('app', 'View'),
                                'class' => 'btn btn-view'
                            ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            if ($model->getDeletable() && $model->status != 'closed') {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['/checkout/payment/cancel-payment', 'id' => $key]), [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '1',
                                    'class' => 'btn btn-danger'
                                ]);
                            }
                        },
                        'update' => function ($url, $model, $key) {
                            return $model->status == 'draft' ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'btn btn-primary']) : '';
                        },
                    ]
                ]
            ],
        ]);
    } else {
        echo "<label>". Yii::t('app', 'No payments created')."</label>";
    }

    echo '<hr>';

    if ($dataProviderFailedPayments->count >= 1) {
        echo '<h3>' . Yii::t('app', 'Failed payments') . '</h3>';
        echo GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $import->getFailedPayments()]),
            'columns' => [
                ['class' => SerialColumn::class],

                [
                    'label' => Yii::t('app', 'Customer'),
                    'attribute' => 'customer_code',
                    'value' => function ($model) {
                        $customer = Customer::findOne(['code' => $model->customer_code]);
                        if ($customer) {
                            return Html::a($customer->fullName, ['/sale/customer/view', 'id' => $customer->customer_id]);
                        } else {
                            return '<label>' . Yii::t('app', 'Customer code not found') . $model->customer_code . ' </label>';
                        }
                    },
                    'format' => 'raw',
                ],
                'customer_code',
                'date',
                'cbu',
                'error',
                'amount'
            ],
        ]);
    echo '<hr>';
    } ?>
</div>
