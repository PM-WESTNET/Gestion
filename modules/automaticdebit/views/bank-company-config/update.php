<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\BankCompanyConfig */

$this->title = Yii::t('app', 'Update Bank Company Config: ' . $model->bank_company_config_id, [
    'nameAttribute' => '' . $model->bank_company_config_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Company Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bank_company_config_id, 'url' => ['view', 'id' => $model->bank_company_config_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bank-company-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
