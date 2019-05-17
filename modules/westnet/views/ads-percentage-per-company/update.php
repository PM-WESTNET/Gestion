<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\AdsPercentagePerCompany */

$this->title = Yii::t('app', 'Update Ads Percentage Per Company: ' . $model->percentage_per_company_id, [
    'nameAttribute' => '' . $model->percentage_per_company_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ads Percentage Per Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->percentage_per_company_id, 'url' => ['view', 'id' => $model->percentage_per_company_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ads-percentage-per-company-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
