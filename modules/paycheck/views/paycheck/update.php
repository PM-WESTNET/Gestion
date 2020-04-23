<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Paycheck */
$this->title = Yii::t('app', 'Update') . " - " . Yii::t('paycheck', 'Paycheck') . " " .
    ($model->is_own ?
            $model->checkbook->moneyBoxAccount->moneyBox->name . " - " . $model->checkbook->moneyBoxAccount->number
        :
            $model->moneyBox->name
    );

$this->params['breadcrumbs'][] = ['label' => Yii::t('paycheck', 'Paychecks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->paycheck_id, 'url' => ['view', 'id' => $model->paycheck_id]];
$this->params['breadcrumbs'][] = Yii::t('paycheck', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
        <div class="paycheck-update">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'for_payment' => true,
                'embed' => $embed,
                'from_thrid_party' => $from_thrid_party
            ]) ?>

        </div>
    </div>
</div>

