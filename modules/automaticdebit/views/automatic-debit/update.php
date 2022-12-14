<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\AutomaticDebit */

$this->title = Yii::t('app', 'Update Automatic Debit: {customer}', ['customer' => $model->customer->fullName]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Automatic Debits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer->fullName, 'url' => ['view', 'id' => $model->automatic_debit_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="automatic-debit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'banks' => $banks
    ]) ?>

</div>
