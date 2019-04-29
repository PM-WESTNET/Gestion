<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\PartnerDistributionModel */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Partner Distribution Model',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partner Distribution Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->partner_distribution_model_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="partner-distribution-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
