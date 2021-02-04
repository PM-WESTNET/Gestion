<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataCompanyConfig */

$this->title = Yii::t('app', 'Update Firstdata Company Config: ' . $model->firstdata_company_config_id, [
    'nameAttribute' => '' . $model->firstdata_company_config_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Company Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->firstdata_company_config_id, 'url' => ['view', 'id' => $model->firstdata_company_config_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="firstdata-company-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies,

    ]) ?>

</div>
