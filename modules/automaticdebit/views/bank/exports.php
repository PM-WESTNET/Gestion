<?php
$this->title = $bank->name. ' - '. Yii::t('app','Exports');
?>

<div class="bank_export">

    <h1><?php echo $this->title ?></h1>

    <p>

    </p>


    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => \yii\grid\SerialColumn::class],

            'timestamp:datetime',

            [
                'class' => \yii\grid\ActionColumn::class,
                'buttons' => [
                    'view',
                    'download' => function ($url, $model) {
                        return \yii\helpers\Html::a('<span class="glyphicon-download-alt"></span>',
                            ['/automaticdebit/bank/dowload', 'export_id' => $model->direct_debit_export_id],
                            ['class' => 'btn btn-warning']);
                    }
                ],
                'template' => '{view} {download}'
            ]
        ]
    ])?>

</div>
