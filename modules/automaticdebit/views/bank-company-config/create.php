<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\BankCompanyConfig */

$this->title = Yii::t('app', 'Add Company to {bank}', ['bank' => $bank->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banks for Automatic Debit'), 'url' => ['bank/index']];
$this->params['breadcrumbs'][] = ['label' => $bank->name, 'url' => ['bank/view', 'id' => $bank->bank_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add Company');
?>
<div class="bank-company-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
