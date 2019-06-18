<?php

$this->title = Yii::t('app','Export').': '.$import->bank->name. ' - '. Yii::$app->formatter->asDate($import->create_timestamp, 'dd-MM-yyyy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Banks for Automatic Debit'), 'url' => ['/automaticdebit/bank/index']];
$this->params['breadcrumbs'][] = ['label' => $import->bank->name, 'url' => ['/automaticdebit/bank/view', 'id' => $import->bank->bank_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Exports'), 'url' => ['/automaticdebit/bank/exports', 'bank_id' => $import->bank->bank_id]];
$this->params['breadcrumbs'][] = Yii::$app->formatter->asDate($import->create_timestamp, 'dd-MM-yyyy');
?>

<div class="export">

    <h1 class="title"><?php echo $this->title ?></h1>

    <p>
        <?php echo \yii\helpers\Html::a(
            '<span class="glyphicon glyphicon-download"></span> '. Yii::t('app','Download'),
            ['/automaticdebit/bank/download-import', 'import_id' => $import->direct_debit_import_id],
            ['class' => 'btn btn-warning', 'target' => '_blank'])?>
    </p>

    <?php echo \yii\widgets\DetailView::widget([
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

</div>
