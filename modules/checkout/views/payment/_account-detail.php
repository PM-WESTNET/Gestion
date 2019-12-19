<?php
/**
 * @var \app\modules\checkout\models\search\PaymentSearch $searchModel
 */


$debt = $searchModel->accountTotalCredit();
$payed = $searchModel->accountPayed(null, null, true);

$total = $searchModel->accountTotal();
$real_total = $searchModel->accountTotal(null, null, false);

if($total < -(Yii::$app->params['account_tolerance'])){
    $totalClass = 'text-danger';
}else{
    $totalClass = 'text-success';
}

if($real_total < -(Yii::$app->params['account_tolerance'])){
    $real_totalClass = 'text-danger';
}else{
    $real_totalClass = 'text-success';
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
            <td><h3 style="margin: 0px"><?= Yii::t('app','Total (Closed Bills)') ?></h3></td>
            <td>
                <h3 class="<?= $totalClass; ?>" style="margin: 0px">
                    <?= Yii::$app->formatter->asCurrency($total); ?>
                    <?= round($total, 2); ?>
                </h3>
            </td>
        </tr>
        <tr>
            <td><h3 style="margin: 0px"><?= Yii::t('app','Total (All Bills)') ?></h3></td>
            <td>
                <h3 class="<?= $real_totalClass; ?>" style="margin: 0px">
                    <?= Yii::$app->formatter->asCurrency($real_total); ?>
                </h3>
            </td>
        </tr>
    </tbody>
</table>
