<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Url;

$this->title = Yii::t('app','Imports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Banks for Automatic Debit'), 'url' => ['/automaticdebit/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $bank->name, 'url' => ['/automaticdebit/bank/view', 'id' => $bank->bank_id]];

?>

<div class="bank_import">

    <h1><?php echo $this->title ?></h1>

    <p>
        <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app','New Import'),
            ['/automaticdebit/bank/create-import', 'bank_id' => $bank->bank_id], ['class' => 'btn btn-success'])?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],
            'create_timestamp:datetime',
            [
                'label' => Yii::t('app','Filename'),
                'value' => function($model){
                    return $model->file;
                }
            ],
            // 'status',
            // 'money_box_account_id',
            [
                'label' => Yii::t('app', 'Company'),
                'value' => function($model){
                    return $model->company->name;
                }
            ],
            [
                'class' => ActionColumn::class,
                'buttons' => [
                    'view'=> function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/automaticdebit/bank/import-view', 'import_id' => $model->debit_direct_import_id]);
                    },
                    'delete'=> function ($url, $model) {
                        if($model->areAllPaymentsPendingToClose()) {
                            return Html::a('<span class="glyphicon glyphicon-remove red"></span>',
                                Url::toRoute(['/automaticdebit/bank/delete-import', 'import_id' => $model->debit_direct_import_id]));
                        }
                        return;
                    },
                ],
                'template' => '{view} {delete} {download}'
            ]
        ]
    ])?>

</div>
