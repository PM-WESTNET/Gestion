<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataAutomaticDebit */

$this->title = Yii::t('app', 'Update Firstdata Automatic Debit: ' . $model->firstdata_automatic_debit_id, [
    'nameAttribute' => '' . $model->firstdata_automatic_debit_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Automatic Debits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->firstdata_automatic_debit_id, 'url' => ['view', 'id' => $model->firstdata_automatic_debit_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="firstdata-automatic-debit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'roles_for_adherence' => $roles_for_adherence,
    ]) ?>

</div>
