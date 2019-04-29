<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorCommission */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('westnet','Vendor Commission'),
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendor Commissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->vendor_commission_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="vendor-commission-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
