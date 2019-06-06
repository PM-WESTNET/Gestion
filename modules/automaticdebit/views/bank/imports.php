<?php
$this->title = $bank->name. ' - '. Yii::t('app','Imports');
?>

<div class="bank_import">

    <h1><?php echo $this->title ?></h1>

    <p>
        <?php echo \yii\helpers\Html::a(
            '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app','New Import'),
            ['/automaticdebit/bank/create-import', 'bank_id' => $bank->bank_id], ['class' => 'btn btn-success'])?>
    </p>


    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => \yii\grid\SerialColumn::class],

            'create_timestamp:datetime',

            [
                'class' => \yii\grid\ActionColumn::class,
                'buttons' => [
                    'view'=> function ($url, $model) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/automaticdebit/bank/import-view', 'import_id' => $model->direct_debit_export_id]);
                    },

                ],
                'template' => '{view} {download}'
            ]
        ]
    ])?>

</div>
