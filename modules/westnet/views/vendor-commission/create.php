<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\VendorCommission */

$this->title = Yii::t('app', 'Create {modelClass}', ['modelClass' => Yii::t('westnet', 'Vendor Commission')]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Commissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-commission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
