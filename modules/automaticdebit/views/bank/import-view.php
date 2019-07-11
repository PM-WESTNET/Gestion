<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use app\modules\sale\models\Customer;

$this->title = Yii::t('app','Import').': '.$import->bank->name. ' - '. Yii::$app->formatter->asDate($import->create_timestamp, 'dd-MM-yyyy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Banks for Automatic Debit'), 'url' => ['/automaticdebit/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $import->bank->name, 'url' => ['/automaticdebit/bank/view', 'id' => $import->bank->bank_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Imports'), 'url' => ['/automaticdebit/bank/exports', 'bank_id' => $import->bank->bank_id]];
$this->params['breadcrumbs'][] = Yii::$app->formatter->asDate($import->create_timestamp, 'dd-MM-yyyy');
?>

<div class="export">

    <?var_dump(Yii::$app->session->getAllFlashes())?>

    <h1 class="title"><?php echo $this->title ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-download"></span> '. Yii::t('app','Download'),
            ['/automaticdebit/bank/download-import', 'import_id' => $import->debit_direct_import_id],
            ['class' => 'btn btn-warning', 'target' => '_blank'])?>
    </p>

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

    <h3><?= Yii::t('app','Payments')?></h3>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider(['query' => $import->getPayments()]),
        'columns' =>  [
            ['class' => SerialColumn::class],
            [
                'attribute' => 'customer.fullName',
                'label' => Yii::t('app','Customer')
            ],
            'status',
            'amount'

        ],
    ])?>
    <hr>

    <h3><?= Yii::t('app','Failed payments')?></h3>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider(['query' => $import->getFailedPayments()]),
        'columns' =>  [
            ['class' => SerialColumn::class],

            [
                'label' => Yii::t('app', 'Customer'),
                'attribute' => 'customer_code',
                'value' => function($model){
                    $customer = Customer::findOne(['code' => $model->customer_code]);
                    if($customer) {
                        return Html::a($customer->fullName, ['/sale/customer/view', 'id' => $customer->customer_id]);
                    } else {
                        return '<label>'.Yii::t('app', 'Customer code not found'). $model->customer_code .' </label>';
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
    ])?>
    <hr>


</div>
