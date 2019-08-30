<?php
$debt = $searchModel->accountTotalCredit();
$payed = $searchModel->accountPayed(null, null, true);

$total = $searchModel->accountTotal();

if($total < -(Yii::$app->params['account_tolerance'])){
    $totalClass = 'text-danger';
}else{
    $totalClass = 'text-success';
}
?>

<h2><?= Yii::t('app', 'Account') ?></h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <td><strong><?= Yii::t('app','Concept') ?></strong></td>
            <td><strong><?= Yii::t('app','Amount') ?></strong></td>
            <?php if ($searchModelAccount) { ?>
                <td><strong><?= Yii::t('app','Account') ?></strong></td>
                <td><strong><?= Yii::t('app','Balance') ?></strong></td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= Yii::t('app','Total credit') ?></td>
            <td><strong><?= Yii::$app->formatter->asCurrency($debt); ?></strong></td>
            <?php if (isset($searchModelAccount)) { ?>
                <td><strong><?= Yii::$app->formatter->asCurrency($searchModelAccount->totalDebit); ?></strong></td>
                <td><strong><?= Yii::$app->formatter->asCurrency($payed - $searchModelAccount->totalCredit); ?></strong></td>
            <?php } ?>
        </tr>
        <tr>
            <td><?= Yii::t('app','Total payed') ?></td>
            <td><strong><?= Yii::$app->formatter->asCurrency($payed); ?></strong></td>
            <?php if (isset($searchModelAccount)) { ?>
                <td><strong><?= Yii::$app->formatter->asCurrency($searchModelAccount->totalCredit); ?></strong></td>
                <td><strong><?= Yii::$app->formatter->asCurrency($debt  - $searchModelAccount->totalDebit); ?></strong></td>
            <?php } ?>
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
