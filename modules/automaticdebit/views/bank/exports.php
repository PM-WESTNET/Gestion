<?php
$this->title = $bank->name. ' - '. Yii::t('app','Exports');
$this->params['breadcrumbs'][] = ['url' => ['/automaticdebit/bank/index'], 'label' => Yii::t('app', 'Banks for Automatic Debit')];
$this->params['breadcrumbs'][] = ['url' => ['/automaticdebit/bank/view', 'id' => $bank->bank_id], 'label' => $bank->name];
$this->params['breadcrumbs'][] = Yii::t('app','Exports');
?>

<div class="bank_export">

    <h1><?php echo $this->title ?></h1>

    <p>
        <?php echo \yii\helpers\Html::a(
            '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app','New Export'),
            ['/automaticdebit/bank/create-export', 'bank_id' => $bank->bank_id], ['class' => 'btn btn-success'])?>
    </p>


    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => \yii\grid\SerialColumn::class],

            [
                'attribute' => 'company_id',
                'value' => function ($model){
                    return $model->company->name;
                }
            ],
            [
                'attribute' => 'type',
                'value' => function ($model){
                    if ($model->type == 'own') {
                        return  Yii::t('app','Bank Customers');
                    }else {
                        return Yii::t('app','Other Customers');
                    }
                }
            ],
            'create_timestamp:datetime',

            [
                'class' => \yii\grid\ActionColumn::class,
                'buttons' => [
                    'view'=> function ($url, $model) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/automaticdebit/bank/export-view', 'export_id' => $model->direct_debit_export_id]);
                    },
                    'download' => function ($url, $model) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download-alt"></span>',
                            ['/automaticdebit/bank/download-export', 'export_id' => $model->direct_debit_export_id],
                            ['target' => '_blank']);
                    }
                ],
                'template' => '{view} {download}'
            ]
        ]
    ])?>

</div>
