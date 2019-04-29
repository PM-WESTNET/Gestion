<?php
$debt = $providerSearch->accountTotalBills();
$payed = $providerSearch->accountTotalPayed();

$total =  $debt - $payed;

if($total > 0.0){
    $totalClass = 'text-danger';
}else{
    $totalClass = 'text-success';
}
?>

<h4><?php //$searchModel->paymentMethod->name; ?></h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <td><strong><?= Yii::t('app','Concept') ?></strong></td>
            <td><strong><?= Yii::t('app','Amount') ?></strong></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= Yii::t('app','Total credit') ?></td>
            <td id="total_bills"><strong><?= Yii::$app->formatter->asCurrency($debt); ?></strong></td>
        </tr>
        <tr>
            <td><?= Yii::t('app','Total payed') ?></td>
            <td id="total_payments"><strong><?= Yii::$app->formatter->asCurrency($payed); ?></strong></td>
        </tr>
        <tr>
            <td><h2><?= Yii::t('app','Total') ?></h2></td>
            <td>
                <h2 class="<?= $totalClass; ?>">
                    <?= Yii::$app->formatter->asCurrency($total); ?>
                </h2>
            </td>
        </tr>
    </tbody>
</table>