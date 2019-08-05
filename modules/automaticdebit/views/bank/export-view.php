<?php

$this->title = Yii::t('app','Export').': '.$export->bank->name. ' - '. Yii::$app->formatter->asDate($export->create_timestamp, 'dd-MM-yyyy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Banks for Automatic Debit'), 'url' => ['/automaticdebit/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $export->bank->name, 'url' => ['/automaticdebit/bank/view', 'id' => $export->bank->bank_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Exports'), 'url' => ['/automaticdebit/bank/exports', 'bank_id' => $export->bank->bank_id]];
$this->params['breadcrumbs'][] = Yii::$app->formatter->asDate($export->create_timestamp, 'dd-MM-yyyy');
?>

<div class="export">

    <h1 class="title"><?php echo $this->title ?></h1>

    <p>
        <?php echo \yii\helpers\Html::a(
            '<span class="glyphicon glyphicon-download"></span> '. Yii::t('app','Download'),
            ['/automaticdebit/bank/download-export', 'export_id' => $export->direct_debit_export_id],
            ['class' => 'btn btn-warning', 'target' => '_blank'])?>
    </p>

    <?php echo \yii\widgets\DetailView::widget([
        'model' => $export,
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

</div>
